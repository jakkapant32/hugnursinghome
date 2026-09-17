<?php

require_once __DIR__ . '/i18n.php';

function hug_localize_service(array $row): array
{
    if (hug_current_lang() === 'th') {
        return $row;
    }
    $key = $row['title'] ?? '';
    $title = hug_t('service.' . $key . '.title');
    $desc = hug_t('service.' . $key . '.desc');
    if ($title !== 'service.' . $key . '.title') {
        $row['title'] = $title;
    }
    if ($desc !== 'service.' . $key . '.desc' && isset($row['description'])) {
        $row['description'] = $desc;
    }

    return $row;
}

function hug_localize_news_card(array $row): array
{
    if (hug_current_lang() === 'th') {
        return $row;
    }
    $origTitle = $row['title'] ?? '';
    $cat = $row['category'] ?? '';
    $catEn = hug_t('category.' . $cat);
    if ($catEn !== 'category.' . $cat) {
        $row['category'] = $catEn;
    }
    $titleEn = hug_t('news_item.' . $origTitle . '.title');
    if ($titleEn !== 'news_item.' . $origTitle . '.title') {
        $row['title'] = $titleEn;
    }
    if (isset($row['content'])) {
        $bodyEn = hug_t('news_item.' . $origTitle . '.content');
        if ($bodyEn !== 'news_item.' . $origTitle . '.content') {
            $row['content'] = $bodyEn;
        }
    }

    return $row;
}

function hug_format_display_date(?string $date): string
{
    if ($date === null || $date === '') {
        return '';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '';
    }

    return date('j F Y', $ts);
}
