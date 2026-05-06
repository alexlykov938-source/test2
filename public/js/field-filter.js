(function () {
  'use strict';

  /**
   * Найти select «Тип» на странице.
   *
   * Стратегия поиска (от точного к общему):
   * 1. По атрибуту name="type" или id="type"
   * 2. По первому <select> рядом с текстом «Тип»
   * 3. Fallback — первый <select> на странице
   *
   * Такой порядок делает скрипт устойчивым к разным вёрсткам.
   */
  function findTypeSelect() {
    // Попытка 1: явные атрибуты
    const byAttr = document.querySelector('select[name="type"], select[id="type"]');
    if (byAttr) return byAttr;

    // Попытка 2: ищем <select> рядом с лейблом «Тип»
    const allSelects = Array.from(document.querySelectorAll('select'));
    const byLabel = allSelects.find((select) => {
      // Смотрим на текст соседних элементов (предыдущий sibling или родитель)
      const parent = select.closest('tr, div, li, p, label') || select.parentElement;
      return parent && /тип/i.test(parent.textContent);
    });
    if (byLabel) return byLabel;

    // Fallback: первый select на странице
    return allSelects[0] || null;
  }

  /**
   * Найти «обёртку» поля — ближайший контейнер, который содержит
   * и лейбл, и сам input. Скрываем его, а не голый input.
   */
  function findFieldWrapper(element) {
    // Типичные контейнеры полей в HTML-формах
    return (
      element.closest('tr') ||       // таблица: <tr><td>Лейбл</td><td><input></td></tr>
      element.closest('li') ||       // список: <li><label>...</label><input></li>
      element.closest('.field') ||   // div с классом
      element.closest('.form-group') ||
      element.parentElement          // fallback: просто родитель
    );
  }

  /**
   * Применить фильтр: показать поля, чей name содержит selectedValue,
   * скрыть все остальные.
   *
   * @param {string} selectedValue — текущее значение select («1», «2», ...)
   * @param {Element[]} fields     — все элементы с атрибутом [name]
   * @param {Element} typeSelect   — сам select «Тип» (его не скрываем)
   */
  function applyFilter(selectedValue, fields, typeSelect) {
    fields.forEach((field) => {
      // Пропускаем сам select «Тип» — он всегда виден
      if (field === typeSelect) return;

      const wrapper = findFieldWrapper(field);
      if (!wrapper) return;

      // Показываем поле, если его name содержит выбранное значение
      const isVisible = field.name.includes(selectedValue);
      wrapper.style.display = isVisible ? '' : 'none';
    });
  }

  /**
   * Инициализация — точка входа.
   * Запускается после загрузки DOM.
   */
  function init() {
    const typeSelect = findTypeSelect();

    if (!typeSelect) {
      console.warn('[field-filter] Не найден select «Тип» на странице.');
      return;
    }

    // Все элементы с атрибутом name (input, select, textarea)
    const fields = Array.from(document.querySelectorAll('[name]'));

    if (fields.length === 0) {
      console.warn('[field-filter] Не найдены поля с атрибутом [name].');
      return;
    }

    // Применить фильтр при изменении select
    typeSelect.addEventListener('change', function () {
      applyFilter(this.value, fields, typeSelect);
    });

    // Применить фильтр сразу — по текущему значению select
    // (важно, если select имеет дефолтное значение при загрузке)
    if (typeSelect.value) {
      applyFilter(typeSelect.value, fields, typeSelect);
    }

    console.info(
      `[field-filter] Инициализирован. Select найден: <${typeSelect.tagName} name="${typeSelect.name}">.`,
      `Полей для фильтрации: ${fields.length - 1}.`
    );
  }

  // Запускаем после загрузки DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    // DOM уже загружен (например, скрипт вставлен через консоль)
    init();
  }
})();