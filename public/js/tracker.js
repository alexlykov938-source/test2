/**
 * tracker.js — сниппет счётчика посещений
 *
 * Подключение к любому сайту:
 *   <script src="https://your-server.com/js/tracker.js" defer></script>
 *
 * ─── Что собираем ────────────────────────────────────────────────────────────
 * 1. Город       → ipapi.co (бесплатно, без токена, 1000 запросов/день)
 * 2. Устройство  → navigator.userAgent (парсим сами, без библиотек)
 * 3. Страница    → window.location.href
 *
 * ─── Почему ipapi.co? ────────────────────────────────────────────────────────
 * + Бесплатно без токена
 * + HTTPS
 * + Отдаёт ip + city в одном запросе
 * ipinfo.io — нужен токен, ip-api.com — только HTTP на бесплатном плане.
 *
 * ─── Почему не библиотека для UserAgent? ─────────────────────────────────────
 * ua-parser-js (~17 КБ) избыточен: нам нужно лишь mobile/tablet/desktop.
 * Три регулярки решают задачу без зависимостей.
 *
 * ─── Почему не отправляем IP на сервер? ──────────────────────────────────────
 * IP определяет сам сервер через $request->ip(). Это исключает подделку.
 *
 * ─── fetch + keepalive vs sendBeacon ─────────────────────────────────────────
 * fetch + keepalive:true — поддерживает JSON-заголовки и тело.
 * sendBeacon — только Blob/FormData, но гарантирует доставку при закрытии.
 * Выбран fetch для простоты и читаемости формата данных.
 */

(function () {
  'use strict';

  // Заменить на реальный URL при деплое
  var TRACK_ENDPOINT = '/api/track';

  /**
   * Определить тип устройства по UserAgent.
   * Возвращает: 'mobile' | 'tablet' | 'desktop'
   */
  function detectDevice() {
    var ua = navigator.userAgent;

    // iPadOS 13+ маскируется под MacOS, но имеет сенсорный экран
    if (navigator.maxTouchPoints > 1 && /Macintosh/i.test(ua)) {
      return 'tablet';
    }

    if (/tablet|ipad|playbook|silk/i.test(ua)) return 'tablet';
    if (/mobile|android|iphone|ipod|blackberry|opera mini|iemobile/i.test(ua)) return 'mobile';
    return 'desktop';
  }

  /**
   * Получить город через ipapi.co (IP не нужен — сервер сам определит).
   * При ошибке — возвращаем заглушку, не блокируем отправку.
   */
  function getGeoData() {
    return fetch('https://ipapi.co/json/', { mode: 'cors' })
      .then(function (res) {
        if (!res.ok) throw new Error('ipapi error: ' + res.status);
        return res.json();
      })
      .then(function (data) {
        return { city: data.city || 'unknown' };
      })
      .catch(function () {
        return { city: 'unknown' };
      });
  }

  /**
   * Отправить данные на сервер.
   * fetch + keepalive:true — JSON + гарантия доставки при закрытии вкладки.
   */
  function sendVisit(payload) {
    return fetch(TRACK_ENDPOINT, {
      method:    'POST',
      headers:   { 'Content-Type': 'application/json' },
      body:      JSON.stringify(payload),
      keepalive: true,
    }).catch(function (err) {
      console.warn('[tracker] Failed to send visit:', err.message);
    });
  }

  /**
   * Главная функция — собирает данные и отправляет на сервер.
   */
  function track() {
    getGeoData().then(function (geo) {
      sendVisit({
        city:     geo.city,
        device:   detectDevice(),
        page:     window.location.href,
        referrer: document.referrer || null,
      });
    });
  }

  // Запуск после загрузки DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', track);
  } else {
    track();
  }
})();