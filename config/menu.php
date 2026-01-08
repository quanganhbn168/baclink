<?php

return [
    // DASHBOARD
    [
        'title' => 'Dashboard',
        'icon' => 'bi bi-speedometer',
        'route' => 'admin.dashboard',
        'permission' => 'view-dashboard',
    ],

    // ===== HỘI VIÊN (MỚI) =====
    [
        'title' => 'Quản lý Hội viên',
        'icon' => 'bi bi-person-badge-fill',
        'route' => 'admin.members.index',
        'active_pattern' => 'admin.members.*',
    ],

    // ===== GIỚI THIỆU (TOP LEVEL) =====
    [
        'title' => 'Quản lý Giới thiệu',
        'icon' => 'bi bi-info-circle-fill',
        'route' => 'admin.intros.index',
        'active_pattern' => 'admin.intros.*',
    ],

    // ===== NHÓM 1: SẢN PHẨM & DỊCH VỤ =====
    ['type' => 'header', 'title' => 'SẢN PHẨM'],

    [
        'title' => 'Quản lý sản phẩm',
        'icon' => 'bi bi-boxes',
        'permission' => 'manage-products',
        'active_pattern' => ['admin.products.*', 'admin.categories.*', 'admin.attributes.*'],
        'submenu' => [
            ['title' => 'Danh mục sản phẩm', 'route' => 'admin.categories.index', 'active_pattern' => 'admin.categories.*'],
            ['title' => 'Sản phẩm',           'route' => 'admin.products.index',  'active_pattern' => 'admin.products.*'],
            // ['title' => 'Thuộc tính',         'route' => 'admin.attributes.index','active_pattern' => 'admin.attributes.*'],
        ],
    ],
    // ===== NHÓM 1: Đơn hàng =====
    /*['type' => 'header', 'title' => 'Đơn hàng'],

    [
        'title' => 'Quản lý Đơn hàng',
        'icon' => 'bi bi-boxes',
        'route' => 'admin.orders.index',
        'active_pattern' => 'admin.orders.*',
    ],*/
    
    /*[
        'title' => 'Quản lý dịch vụ',
        'icon' => 'bi bi-briefcase',
        'active_pattern' => ['admin.service_categories.*', 'admin.services.*'],
        'submenu' => [
            ['title' => 'Danh mục dịch vụ', 'route' => 'admin.service_categories.index', 'active_pattern' => 'admin.service_categories.*'],
            ['title' => 'Dịch vụ',          'route' => 'admin.services.index',          'active_pattern' => 'admin.services.*'],
        ],
    ],*/

    // ===== NHÓM 2: NỘI DUNG WEBSITE =====
    ['type' => 'header', 'title' => 'NỘI DUNG WEBSITE'],

    [
        'title' => 'Quản lý bài viết',
        'icon' => 'bi bi-pencil-square',
        'permission' => 'manage-posts',
        'active_pattern' => ['admin.post-categories.*', 'admin.posts.*'],
        'submenu' => [
            ['title' => 'Danh mục bài viết', 'route' => 'admin.post-categories.index', 'active_pattern' => 'admin.post-categories.*'],
            ['title' => 'Bài viết',          'route' => 'admin.posts.index',           'active_pattern' => 'admin.posts.*'],
        ],
    ],
    [
        'title' => 'Quản lý tập tin',
        'icon' => 'bi bi-folder-fill',
        'route' => 'admin.file-manager.index',
        'active_pattern' => 'admin.file-manager.*',
    ],
    [
        'title' => 'Thư viện & Hiển thị',
        'icon' => 'bi bi-images',
        'active_pattern' => ['admin.slides.*', 'admin.testimonials.*', 'admin.intros.*', 'admin.brands.*'],
        'submenu' => [
            ['title' => 'Slide trang chủ',  'route' => 'admin.slides.index',        'active_pattern' => 'admin.slides.*'],
            ['title' => 'Feedback (Testimonial)', 'route' => 'admin.testimonials.index', 'active_pattern' => 'admin.testimonials.*'],
// ['title' => 'Giới thiệu (Pages)',     'route' => 'admin.intros.index',       'active_pattern' => 'admin.intros.*'],
            ['title' => 'Thương hiệu (Brand)',    'route' => 'admin.brands.index',       'active_pattern' => 'admin.brands.*'],
            ['title' => 'Khối nội dung (Content Block)',    'route' => 'admin.content-blocks.index',       'active_pattern' => 'admin.content-blocks.*'],
            ['title' => 'Chính sách (Pages)',    'route' => 'admin.pages.index',       'active_pattern' => 'admin.pages.*'],
            [
        'title' => 'Quản lý Menu',
        'icon' => 'bi bi-menu-button-wide-fill', // Icon menu ngang
        'route' => 'admin.menus.index',
        'active_pattern' => 'admin.menus.*',
        // 'permission' => 'manage-menus', // Bật lên nếu anh có check quyền
    ],
        ],
    ],

    // ===== NHÓM 3: DỰ ÁN & LĨNH VỰC =====
    // ['type' => 'header', 'title' => 'DỰ ÁN & LĨNH VỰC'],

    // [
    //     'title' => 'Quản lý Dự án',
    //     'icon' => 'bi bi-kanban',
    //     'active_pattern' => ['admin.project-categories.*', 'admin.projects.*'],
    //     'submenu' => [
    //         ['title' => 'Danh mục Dự án', 'route' => 'admin.project-categories.index', 'active_pattern' => 'admin.project-categories.*'],
    //         ['title' => 'Dự án',          'route' => 'admin.projects.index',           'active_pattern' => 'admin.projects.*'],
    //     ],
    // ],
    [
        'title' => 'Quản lý Lĩnh vực',
        'icon' => 'bi bi-journal-richtext',
        'permission' => 'manage-fields',
        'active_pattern' => ['admin.field-categories.*', 'admin.fields.*'],
        'submenu' => [
            ['title' => 'Danh mục Lĩnh vực', 'route' => 'admin.field-categories.index', 'active_pattern' => 'admin.field-categories.*'],
            ['title' => 'Lĩnh vực',          'route' => 'admin.fields.index',           'active_pattern' => 'admin.fields.*'],
        ],
    ],

    // ===== NHÓM 4: ĐỐI TÁC & TUYỂN DỤNG =====
    ['type' => 'header', 'title' => 'ĐỐI TÁC & TUYỂN DỤNG'],

    /*[
        'title' => 'Quản lý Đại lý',
        'icon' => 'bi bi-people',
        'active_pattern' => ['admin.agents.*','admin.dealer-applications.*'],
        'permission' => 'manage-agents',
        'submenu' => [
            ['title' => 'Danh sách đại lý',  'route' => 'admin.agents.index', 'active_pattern' => 'admin.agents.*'],
            ['title' => 'Đăng ký đại lý', 'route' => 'admin.dealer-applications.index', 'active_pattern' => 'admin.dealer-applications.*'],
        ],
        // 'badge' => ['text' => '2', 'class' => 'badge-info'], // nếu muốn hiện số pending
    ],
    [
        'title' => 'Quản lý Tuyển dụng',
        'icon' => 'bi bi-briefcase-fill',
        'route' => 'admin.careers.index',
        'active_pattern' => 'admin.careers.*',
        'permission' => 'manage-careers',
    ],*/

    // ===== NHÓM 5: HỆ THỐNG =====
    ['type' => 'header', 'title' => 'HỆ THỐNG'],

    [
        'title' => 'Quản lý người dùng',
        'icon' => 'bi bi-people-fill',
        'permission' => 'manage-users',
        'active_pattern' => 'admin.users.*',
        'submenu' => [
            ['title' => 'Danh sách người dùng', 'route' => 'admin.users.index',  'active_pattern' => 'admin.users.*'],
            ['title' => 'Thêm người dùng',      'route' => 'admin.users.create', 'active_pattern' => 'admin.users.create'],
        ],
    ],
    [
        'title' => 'Phân quyền',
        'icon' => 'bi bi-shield-lock-fill',
        'permission' => 'manage-roles',
        'active_pattern' => 'admin.roles.*',
        'submenu' => [
            ['title' => 'Vai trò & Quyền', 'route' => 'admin.roles.index', 'active_pattern' => 'admin.roles.*'],
        ],
    ],
    [
        'title' => 'Cấu hình',
        'icon' => 'bi bi-gear-fill',
        'route' => 'admin.settings.index',
        'active_pattern' => 'admin.settings.*',
        'permission' => 'manage-settings',
    ],
];
