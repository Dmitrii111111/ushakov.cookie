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


  function handleContentLoaded (options) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function () {
        insertCookieDiv(options.data)
      })
    }
    else {
      insertCookieDiv(options.data)
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

    let cookieText = document.createElement('div')
    cookieText.className = 'ushakov-cookie__text'

    cookieText.innerHTML = options.text
    if (options.color) {
      let anchor = cookieText.querySelector('a')
      if (anchor) {
        anchor.style.color = options.color
      }
    }

    if (options.textColor) {
      cookieText.style.color = options.textColor
    }

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
