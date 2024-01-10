<?php

function setLang($locale) {
  global $lang_meta;
  global $lang_descr;
  global $lang_title;
  global $lang_main;
  global $lang_main2;
  global $lang_main3;
  global $lang_main4;
  global $lang_main5;
  global $lang_err1;
  global $lang_err2;
  global $lang_err4;
  global $lang_err5;
  global $lang_err6;
  global $lang_cr1;
  global $lang_cr2;
  global $lang_cr3;
  global $lang_ret;
  global $lang_conf;
  global $lang_open;
  global $lang_text;
  global $lang_cls;
  switch ($locale) {
    case 1:
      $lang_meta="uk";
      $lang_descr="Безпечно дiлись важливими данними";
      $lang_title="Безпечнi повiдомлення";
      $lang_main="Безпечна вiдправка будь яких текстових данних:";
      $lang_main2="введiть вашi важливi даннi i отримайте одноразове посилання на них. Подiлiться цим посиланням з будь ким, будь яким методом. Ваш спiвбесiдник зможе вiдкрити це посилання лише один раз. Даннi не передаються у вiдкритому виглядi, ви лише дiлитесь посиланням. Пiсля вiдкриття все даннi знищуються, тому це посилання не можна вiдкрити вдруге - це i є гарантiєю, що вашi секретнi даннi не були нiким перехопленi та вiдкритi.";
      $lang_main3="Введiть ваш текст сюди";
      $lang_main4="Час життя посилання(годин)";
      $lang_main5="Створити посилання";
      $lang_err1="Помилка! Ви не ввели текст, або ще щось.";
      $lang_err2="Помилка CSRF token. Оновiть сторiнку i спробуйте ще раз.";
      $lang_err4="Упс! Проблема:";
      $lang_err5="Таке посилання просто не icнує. 🤷";
      $lang_err6="Строк життя посилання закiнчився. Воно бiльше не доступно, як i даннi, що були там.";
      $lang_cr1="Ось ваше тимчасове посилання:";
      $lang_cr2="Скопiюйте його та передайте кому вважаєте за потрiбне. Це посилання можна буде вiдкрити тiльки один раз.";
      $lang_cr3="Створити ще одне";
      $lang_ret="Повернутися на головну сторiнку";
      $lang_conf="Пiдтвердiть вiдкриття посилання.<br>Пiсля цього воно буде показано один раз и видалено:";
      $lang_open="Вiдкрити посилання";
      $lang_text="Ось текст що був зашифрован:";
      $lang_cls="Закрити вкладку";
      break;
    case 2:
      $lang_meta="ru";
      $lang_descr="Безопасно делись важными данными";
      $lang_title="Безопасные сообщения";
      $lang_main="Безпасная отправка любых текстовых данных:";
      $lang_main2="введите ваши важные данные и получите одноразовую ссылку на них. Поделитесь этой ссылкой с кем угодно, любым способом. Ваш собеседник сможет открыть эту ссылку только один раз. Данные не передаются в открытом виде, вы лишь делитесь ссылкой. После открытия все данные уничтожаются, потому эту ссылку нельзя открыть снова - это и есть гарантией, что ваши секретные данные не были никем перехвачены и открыты.";
      $lang_main3="Введите ваш текст сюда";
      $lang_main4="Время жизни ссылки(часов)";
      $lang_main5="Создать ссылку";
      $lang_err1="Ошибка! Вы не ввели текст, или еще что-то.";
      $lang_err2="Ошибка CSRF token. Обновите страницу и попробуйте еще раз.";
      $lang_err4="Упс! Проблема:";
      $lang_err5="Такая ссылка просто не существует 🤷";
      $lang_err6="Срок жизни ссылки закончился. Оно больше не доступно, як и данные, что были там.";
      $lang_cr1="вот ваша временная ссылка:";
      $lang_cr2="Скопируйте его и передайте кому считаете нужным. Эта ссылка может быть открыта только один раз.";
      $lang_cr3="Создать еще одно";
      $lang_ret="Вернуться на главную страницу";
      $lang_conf="Подтвердите открытие ссылки.<br>После этого оно будет показано один раз и удалено:";
      $lang_open="Открыть ссылку";
      $lang_text="Вот текст что был зашифрован:";
      $lang_cls="Закрыть вкладку";
      break;
    case 3:
      $lang_meta="en";
      $lang_descr="Safely share your data";
      $lang_title="Secuire messages";
      $lang_main="Secure send your important data:";
      $lang_main2="enter your text data and get one time link for it. Share this link with anybody you want via any method you want. The receiver will be able to open the link only once. Your data doesn't being tranfered by plaintext, you are just sharing the link. After link is opened,the data is being destroyed, so this link can't be opened again anymore - is guarantees your secret data wasn't intercepted and opened by somebody.";
      $lang_main3="Enter your text data here";
      $lang_main4="Link lifetime(hours)";
      $lang_main5="Create link";
      $lang_err1="Error! You haven't entered text, or something else.";
      $lang_err2="CSRF token error. Reload the page and try again.";
      $lang_err4="Oops! A problem:";
      $lang_err5="This link doesn't exist anymore! 🤷";
      $lang_err6="Link's lifetime has expired. It is unavailable, like the data it contained.";
      $lang_cr1="this is your temporarily link:";
      $lang_cr2="Copy it and share with anybody you want. This link can be opened just once.";
      $lang_cr3="Create another one";
      $lang_ret="Return to main page";
      $lang_conf="Confirm link opening.<br>After that you will see the stored message once and it will be deleted.";
      $lang_open="Open link";
      $lang_text="Here is your data that was encrypted:";
      $lang_cls="Close the tab";
      break;
  }
}
