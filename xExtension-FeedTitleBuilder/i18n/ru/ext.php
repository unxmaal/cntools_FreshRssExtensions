<?php

return array(
	'FeedTitleBuilder' => array(
		'label4Template' => 'Шаблон заголовка ленты',
		'helping' => 'Есть несколько специальных разделов, которые позволяют настроить персональный заголовок ленты.',
		'help4origtitle' => 'Используйте этот код, чтобы определить, где должен быть показан оригинальный заголовок этой ленты',
		'help4date' => 'С помощью этого кода вы можете добавить дату дня в свой любимый формат. Используйте вашу любимую систему веб-поиска, чтобы определить, какие параметры доступны в PHP функции даты.<br />Ищите подробности: <a href="https://www.php.net/manual/en/function.date.php" target="_blank" rel="noopener nofollow">https://www.php.net/manual/en/function.date.php</a><br />Пример: {date}ymd{/date} => 20190401',
		'help4phpparseurl' => 'URL будет разделен с помощью функции PHP <code>parse_url</code> и вы можете получить определенное значение результата.<br />Подробнее: <a href="https://www.php.net/manual/en/function.parse-url.php" target="_blank" rel="noopener nofollow">https://www.php.net/manual/en/function.parse-url.php</a>',
		'help4phpparseurlvalues' => 'Доступны следующие специальные слова:',
		'help4phpparseurlschema' => 'Вы получите \'HTTP\', \'HTTPS\', \'FTP\' или друг друга. Например, \'HTTPS\'',
		'help4phpparseurlhost' => 'В примере вы получите \'github.com\'.',
		'help4phpparseurlport' => 'Вы получите порт, который находится в определенном URL.',
		'help4phpparseurluser' => 'Вы получите пользователя, который находится в определенном URL.',
		'help4phpparseurlpass' => 'Вы получите пароль, который находится в определенном URL.',
		'help4phpparseurlpath' => 'В примере вы получите \'/FreshRSS/FreshRSS\'.',
		'help4phpparseurlquery' => 'Вы получите текст между \'?\' и \'#\'.',
		'help4phpparseurlfragment' => 'Вы получите текст после \'#\'.',
		'help4phpparseurlhostspecial' => 'Возможно, ключевое слово \'host\' слишком много, так что вы можете использовать следующие специальные слова. Эти значения разделяются значениями «.» (точка) «host».',
		'help4phpparseurlhostsub' => 'Вы получите полный текст перед предпоследней точкой.',
		'help4phpparseurlhostname' => 'Вы получите текст между предпоследней и последней точками.',
		'help4phpparseurlhosttld' => 'Вы получите текст после последней точки.',
		'example' => 'Пример',
		'example_template_code' => 'Код шаблона:',
		'example_url' => 'URL:',
		'example_title' => 'Сгенерированный заголовок ленты:',
		'info' => 'Информация:',
		'infodesc' => 'Если шаблон пуст, вы получите оригинальный заголовок добавленной ленты!',
	),
);