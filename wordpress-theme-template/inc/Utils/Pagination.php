<?php

namespace ThemeName\Utils;

class Pagination
{
    private const RANGE = 1;
    private const FIRST_PAGES = 3;
    private const LAST_PAGES = 3;

    public static function getPaginationConfig(): array
    {
        return [
            'range' => self::RANGE,
            'first_pages' => self::FIRST_PAGES,
            'last_pages' => self::LAST_PAGES,
        ];
    }

    public static function calculatePaginationPages($paged, $pages): array
    {
        $config = self::getPaginationConfig();
        $showPages = [];

        $showPages[] = 1;

        if ($paged === 1 && $pages >= 3) {
            $showPages[] = 2;
            if ($pages >= 4) {
                $showPages[] = 3;
            }
        }

        if ($paged != 1) {
            $showPages[] = $paged;
        }

        for ($i = 1; $i <= $config['range']; $i++) {
            if ($paged - $i > 1) {
                $showPages[] = $paged - $i;
            }
            if ($paged + $i < $pages) {
                $showPages[] = $paged + $i;
            }
        }

        if ($pages > 1) {
            $showPages[] = $pages;
        }

        $uniquePages = array_unique($showPages);
        sort($uniquePages);

        return $uniquePages;
    }

    public static function generatePaginationHtml($paged, $pages, $is_js = false, $scroll_target = '#'): void
    {
        $uniquePages = self::calculatePaginationPages($paged, $pages);

        echo '<nav class="c-pagination" role="navigation" aria-label="ページナビゲーション"><ul class="c-pagination__list">';

        self::generatePaginationNavigationButtons($paged, $pages, $is_js, $scroll_target, 'prev');
        self::generatePaginationPageNumbers($paged, $uniquePages, $is_js, $scroll_target);
        self::generatePaginationNavigationButtons($paged, $pages, $is_js, $scroll_target, 'next');

        echo '</ul></nav>';
    }

    private static function generatePaginationNavigationButtons($paged, $pages, $is_js, $scroll_target, $direction): void
    {
        $is_prev = ($direction === 'prev');
        $is_first = ($direction === 'prev');

        if ($is_prev) {
            $can_navigate = ($paged > 1);
            $target_page = $can_navigate ? ($is_first ? 1 : $paged - 1) : 1;
            $aria_label = $is_first ? '最初のページへ' : '前のページへ';
            $class_suffix = $is_first ? '--first' : '--prev';
        } else {
            $can_navigate = ($paged < $pages);
            $target_page = $can_navigate ? ($is_first ? $pages : $paged + 1) : $pages;
            $aria_label = $is_first ? '最後のページへ' : '次のページへ';
            $class_suffix = $is_first ? '--last' : '--next';
        }

        $inactive_class = $can_navigate ? '' : ' c-pagination__link--inactive';
        $href = $can_navigate ? ($is_js ? esc_url($scroll_target) : get_pagenum_link($target_page)) : '#';
        $data_page = $is_js ? ' data-page="' . $target_page . '"' : '';

        echo '<li class="c-pagination__item">';
        echo '<a class="c-pagination__link c-pagination__link' . $class_suffix . $inactive_class . '" href="' . $href . '"' . $data_page . ' aria-label="' . $aria_label . '"></a>';
        echo '</li>';
    }

    private static function generatePaginationPageNumbers($paged, $uniquePages, $is_js, $scroll_target): void
    {
        for ($i = 0; $i < count($uniquePages); $i++) {
            $page = $uniquePages[$i];
            $nextPage = isset($uniquePages[$i + 1]) ? $uniquePages[$i + 1] : null;

            if ($page == $paged) {
                echo '<li class="c-pagination__item c-pagination__item--active">';
                echo '<span class="c-pagination__text" aria-current="page">' . $page . '</span>';
                echo '</li>';
            } else {
                $href = $is_js ? esc_url($scroll_target) : get_pagenum_link($page);
                $data_page = $is_js ? ' data-page="' . $page . '"' : '';

                echo '<li class="c-pagination__item">';
                echo '<a class="c-pagination__link" href="' . $href . '"' . $data_page . ' aria-label="ページ ' . $page . ' へ">' . $page . '</a>';
                echo '</li>';
            }

            if ($nextPage && $nextPage - $page > 1) {
                echo '<li class="c-pagination__item"><span class="c-pagination__dots">…</span></li>';
            }
        }
    }

    /**
     * 通常の WP クエリ用ページネーション（サーバーサイド）
     */
    public static function customPagination($pages = '', $paged = 1): void
    {
        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }

        if ($paged == 1) {
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        }

        $pages = intval($pages);
        $paged = intval($paged);

        if ($pages === 1) {
            return;
        }

        self::generatePaginationHtml($paged, $pages, false);
    }

    /**
     * REST API 等から取得した pagination 配列用（JS 連動）
     */
    public static function customPaginationJs($pagination, $scroll_target = '#'): void
    {
        if (!$pagination || !isset($pagination['pages']) || !isset($pagination['paged'])) {
            return;
        }

        $pages = intval($pagination['pages']);
        $paged = intval($pagination['paged']);

        if ($pages <= 1) {
            return;
        }

        self::generatePaginationHtml($paged, $pages, true, $scroll_target);
    }
}
