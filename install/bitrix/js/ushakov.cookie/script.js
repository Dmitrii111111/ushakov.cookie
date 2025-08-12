(function () {
  if (document.cookie.split('; ').find(row => row.startsWith('ushakov_cookie='))) {
    return
  }

  fetch('/bitrix/tools/ushakov_cookie_options.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    credentials: 'same-origin',
    body: new URLSearchParams({
      'SITE_ID': BX.message('SITE_ID'),
    }),
  })
  .then(response => response.json())
  .then(options => {
    handleContentLoaded(options)
  })
  .catch(error => {
    console.error('Ошибка при запросе опций модуля ushakov.cookie', error)
  })


  function handleContentLoaded (response) {
    const cfg = response && response.data ? response.data : {};
    const delay = parseInt(cfg.delayMs, 10);
    const run = () => {
      if (!isNaN(delay) && delay > 0) {
        setTimeout(() => insertCookieDiv(cfg), delay);
      } else {
        insertCookieDiv(cfg);
      }
    };

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', run);
    } else {
      run();
    }
  }

  function insertCookieDiv(options) {
    // Создаем элементы
    let cookieDiv = document.createElement('div')
    cookieDiv.style.zIndex = options.zIndex
    cookieDiv.id = 'ushakov-cookie-wrap'
    cookieDiv.className = 'ushakov-cookie'
    if (options.disableMob === 'Y') {
      cookieDiv.classList.add('ushakov-cookie--d-mob-none')
    }

    // позиция + вертикальный отступ для контейнера
    if (options.position === 'top') {
      cookieDiv.classList.add('ushakov-cookie--pos-top')
      cookieDiv.style.top = options.offsetY || '0'
      cookieDiv.style.bottom = 'auto'          // важно
    } else {
      cookieDiv.classList.add('ushakov-cookie--pos-bottom')
      cookieDiv.style.bottom = options.offsetY || '0'
      cookieDiv.style.top = 'auto'             // важно
    }

    let innerDiv = document.createElement('div')
    innerDiv.classList.add('ushakov-cookie-bg-custom');
    innerDiv.style.setProperty('--ushakov-cookie-bg', options.bgColor);

    innerDiv.style.setProperty('--ushakov-cookie-text-color', options.textColor);

    innerDiv.style.setProperty('--ushakov-cookie-font-size', options.fontSize);

    // радиус
    if (options.borderRadius) {
      innerDiv.style.setProperty('--ushakov-cookie-radius', options.borderRadius);
    }

    // тень (вкл/выкл)
    if (options.shadow === 'Y') {
      innerDiv.style.setProperty('--ushakov-cookie-shadow', '0 8px 24px rgba(0, 0, 0, 0.85)');
    } else {
      innerDiv.style.setProperty('--ushakov-cookie-shadow', 'none');
    }

    // выравнивание
    const align = options.align || 'center';
    cookieDiv.classList.add('ushakov-cookie--align-' + align);

    // макс. ширина и горизонтальные отступы
    innerDiv.style.setProperty('--ushakov-cookie-max-width', options.maxWidth)
    innerDiv.style.setProperty('--ushakov-cookie-offset-x', options.offsetX)

    let cookieText = document.createElement('div')
    cookieText.className = 'ushakov-cookie__text'

    cookieText.innerHTML = options.text

    // Вставляем или img, или span в зависимости от textButton
    let closeElement
    if (options.textButton && options.textButton.trim() !== '') {
      closeElement = document.createElement('span')
      closeElement.classList.add('button')
      closeElement.textContent = options.textButton
    } else {
      closeElement = document.createElement('img')
      closeElement.src = '/bitrix/images/ushakov.cookie/close.svg'
    }
    closeElement.onclick = sendCookieRequestAndRemoveElement

    // Собираем и вставляем в документ
    innerDiv.appendChild(cookieText)
    innerDiv.appendChild(closeElement)
    cookieDiv.appendChild(innerDiv)
    document.body.appendChild(cookieDiv)
  }

  function sendCookieRequestAndRemoveElement () {
    fetch('/bitrix/tools/ushakov_cookie_save.php', {
      method: 'GET', // в оригинале BX.ajax делал GET-запрос, поэтому явно укажем
      credentials: 'same-origin' // аналог BX.ajax: куки и сессия будут отправлены
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok')
      }
      return response.text() // или .json(), если на сервере JSON
    })
    .then(data => {
      console.log('Cookie saved successfully')
      // Если нужно что-то сделать с ответом сервера, делаем это здесь
    })
    .catch(error => {
      console.error('Error saving cookie:', error)
    })
    .finally(() => {
      const element = document.getElementById('ushakov-cookie-wrap')
      if (element) {
        element.remove()
      }
    })
  }
})();
