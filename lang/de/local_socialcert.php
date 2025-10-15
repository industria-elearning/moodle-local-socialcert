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
$string['description'] = 'Ermöglicht es dem Benutzer, sein Zertifikat direkt auf LinkedIn zu teilen.';

// Einstellungen.
$string['organizationid'] = 'LinkedIn-Organisations-ID';
$string['organizationid_desc'] = 'Numerische ID der Firma/Organisation, die von LinkedIn Add-to-Profile verwendet wird. Leer lassen, um die Funktion zu deaktivieren, bis sie konfiguriert ist.';
$string['organizationname'] = 'Name der LinkedIn-Organisation';
$string['organizationname_desc'] = 'Name der Organisation, wie sie auf LinkedIn angezeigt wird. Muss exakt übereinstimmen. Leer lassen, um die Funktion zu deaktivieren, bis sie konfiguriert ist.';

$string['privacy:metadata'] = 'Das Share Certificate AI-Plugin speichert keine personenbezogenen Daten.';

$string['noissue'] = 'Du hast noch kein Zertifikat für diesen Kurs erhalten.';

$string['linkcertbuttontext'] = 'Auf LinkedIn teilen';
$string['copyarticlebuttontext'] = 'LinkedIn-Beitrag kopieren';

$string['shareinstruction'] = 'Feiere deinen Erfolg! Klicke unten, um dein Zertifikat auf LinkedIn zu präsentieren und dein Netzwerk an deinem Erfolg teilhaben zu lassen:';

$string['copyconfirmation'] = 'Kopiert ✔';

$string['airesponsebtn'] = 'KI aktivieren';

$string['generating'] = 'Wird erstellt…';

$string['certificateimage'] = 'certificate.png';

// Share Certificate AI – Teilen (Schritt 1)
$string['sharetitle']        = 'Teile deinen Erfolg auf LinkedIn';
$string['sharesubtitle']     = 'Wir veröffentlichen einen verifizierbaren Link zu deinem Zertifikat.';
$string['buttonlabelshare']  = 'Auf LinkedIn teilen';
$string['whatsharelabel']    = 'Was wird geteilt?';

// Rückmeldung/Status (optional, aber empfohlen)
$string['popupblocked']      = 'Bitte Pop-ups aktivieren, um fortzufahren.';
$string['sharecompleted']    = 'Das Teilen auf LinkedIn wurde abgeschlossen.';

$string['ai_field_heading']  = 'Beitragstext';

$string['certificate_url']   = 'Link';

$string['ai_actioncall']   = 'Erstelle mit einem Klick einen professionellen Text für deinen LinkedIn-Beitrag';

$string['enableai'] = 'KI aktivieren, um vorgeschlagenen Beitragstext zu erzeugen';

$string['enableai_desc'] = 'Wenn diese Option deaktiviert ist, ruft das Plugin Provider AI nicht auf und generiert keine Vorschläge für LinkedIn-Beiträge.';