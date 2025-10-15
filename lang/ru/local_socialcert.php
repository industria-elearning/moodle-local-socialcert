<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// See the GNU General Public License for more details: https://www.gnu.org/licenses/.

/**
 * English strings for local_socialcert.
 *
 * @package   local_socialcert
 * @category  string
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Share Certificate AI';
$string['description'] = 'Позволяет пользователю поделиться своим сертификатом напрямую в LinkedIn.';

// Настройки.
$string['organizationid'] = 'ID организации LinkedIn';
$string['organizationid_desc'] = 'Числовой идентификатор компании или организации, используемый функцией LinkedIn Add-to-Profile. Оставьте поле пустым, чтобы отключить до настройки.';
$string['organizationname'] = 'Название организации в LinkedIn';
$string['organizationname_desc'] = 'Название организации, отображаемое в LinkedIn. Должно точно соответствовать тому, как оно указано на LinkedIn. Оставьте поле пустым, чтобы отключить до настройки.';

$string['privacy:metadata'] = 'Плагин Share Certificate AI не хранит никаких персональных данных.';

$string['noissue'] = 'У вас пока нет выданного сертификата для этого курса.';

$string['linkcertbuttontext'] = 'Поделиться в LinkedIn';
$string['copyarticlebuttontext'] = 'Скопировать публикацию LinkedIn';

$string['shareinstruction'] = 'Отпразднуйте своё достижение! Нажмите ниже, чтобы показать свой сертификат в LinkedIn и поделиться успехом с вашей сетью:';

$string['copyconfirmation'] = 'Скопировано ✔';

$string['airesponsebtn'] = 'Активировать ИИ';

$string['generating'] = 'Генерация…';

$string['certificateimage'] = 'certificate.png';

// Share Certificate AI – Поделиться (Шаг 1)
$string['sharetitle']        = 'Поделитесь своим достижением в LinkedIn';
$string['sharesubtitle']     = 'Мы опубликуем проверяемую ссылку на ваш сертификат.';
$string['buttonlabelshare']  = 'Поделиться в LinkedIn';
$string['whatsharelabel']    = 'Что мы публикуем?';

// Отзывы/статус (необязательно, но рекомендуется)
$string['popupblocked']      = 'Разрешите всплывающие окна, чтобы продолжить.';
$string['sharecompleted']    = 'Публикация в LinkedIn завершена.';

$string['ai_field_heading']  = 'Текст публикации';

$string['certificate_url']   = 'Ссылка';

$string['ai_actioncall']   = 'Создайте профессиональный текст для публикации в LinkedIn одним кликом';
