<?php
/*去除谷歌字体*/
function ashuwp_remove_open_sans() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
    wp_enqueue_style('open-sans','');
}   
add_action('init','ashuwp_remove_open_sans');

/*前台不显示顶部工具条*/
show_admin_bar(false);

/**加载ashuwp_framework框架的文件**/
require get_template_directory() . '/include/ashuwp_framework_core.php';
require get_template_directory() . '/include/ashuwp_options_feild.php';
require get_template_directory() . '/include/ashuwp_termmeta_feild.php';
require get_template_directory() . '/include/ashuwp_postmeta_feild.php';
require get_template_directory() . '/include/import_export.php';

/**注册文章类型范例**/
//require get_template_directory() . '/include/post_type.php';

/**
*ashuwp_framework框架配置文件文件
**/
require get_template_directory() . '/include/config.php';

/*主题中一些常用的函数*/
require get_template_directory() . '/include/function.php';

/**将头像服务器替换为多说**/
function duoshuo_avatar($avatar) {
    $avatar = str_replace(array("www.gravatar.com","0.gravatar.com","1.gravatar.com","2.gravatar.com"),"gravatar.duoshuo.com",$avatar);
    return $avatar;
}
add_filter( 'get_avatar', 'duoshuo_avatar', 10, 3 );

function ashuwp_theme_setup() {
  /*注册菜单*/
  register_nav_menus(
    array(
      'primary' => '主菜单',
      //'second' => '另一个菜单',
    )
  );
  
  //后台编辑器内的样式文件
  add_editor_style( array( 'css/editor-style.css') );
  
  //主题功能
  add_theme_support( 'title-tag' ); //自动title标签
  add_theme_support( 'post-thumbnails',array('post') ); //特色图像
  //add_theme_support( 'post-thumbnails',array('post','post_type') );
  //add_theme_support('custom-background');
  
  //设置特色图像尺寸
  set_post_thumbnail_size( 240, 240, true );
  
  //增加特色图像尺寸
  //add_image_size('taxonomy-size',500,500,true);
  
  //清除默认的相册样式
  add_filter( 'use_default_gallery_style', '__return_false' );
  
  //清除头部标签
  ashuwp_clean_them();
}
add_action( 'after_setup_theme', 'ashuwp_theme_setup' );

/**注册侧边栏**/
function ashuwp_register_sidebar(){
  
	if ( function_exists('register_sidebar') ) {
    register_sidebar(array(
      'name' => 'Sidebar',
      'id' =>'main_sidebar',
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-title">',
      'after_title' => '</h3>',
    ));
  }
}
add_action('widgets_init','ashuwp_register_sidebar');

/*
*清除头部多余标签
*在ashuwp_theme_setup中调用
*/
function ashuwp_clean_them(){
  remove_action( 'wp_head', 'print_emoji_detection_script', 7, 1);
  remove_action( 'wp_print_styles', 'print_emoji_styles', 10, 1);
  remove_action( 'wp_head', 'rsd_link', 10, 1);
  remove_action( 'wp_head', 'wp_generator', 10, 1);
  remove_action( 'wp_head', 'feed_links', 2, 1);
  remove_action( 'wp_head', 'feed_links_extra', 3, 1);
  remove_action( 'wp_head', 'index_rel_link', 10, 1);
  remove_action( 'wp_head', 'wlwmanifest_link', 10, 1);
  remove_action( 'wp_head', 'start_post_rel_link', 10, 1);
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0);
  remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0);
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0);
  remove_action( 'wp_head', 'rest_output_link_wp_head', 10, 0);
  remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10, 1);
  remove_action( 'wp_head', 'rel_canonical', 10, 0);
  remove_filter( 'the_content', 'wptexturize');
  remove_filter( 'the_content', 'wptexturize');
}

/**删除wp-embed.min.js**/
function ashuwp_deregister_scripts(){
  wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'ashuwp_deregister_scripts' );

/**利用wp_head和wp_footer引入css和js**/
function ashuwp_scripts_styles() {
  //css
  wp_enqueue_style( 'theme_custom_style', get_template_directory_uri().'/css/normalize.css', array(), '1.0' );
  wp_enqueue_style( 'theme_custom_style', get_stylesheet_uri(), array(), '1.0' );
  
  //js
	wp_enqueue_script( 'jquery' ); //加载jquery
  //评论
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
  //加载自定义的js文件 最后面的参数为true将会在wp_footer中输出
	wp_enqueue_script( 'theme_custom_js', get_template_directory_uri().'/js/custom.js', array(), '1.0', true);
  
}
add_action( 'wp_enqueue_scripts', 'ashuwp_scripts_styles' );

/**移除后台仪表盘页面的一些板块****/
function remove_dashboard_widgets(){
  global$wp_meta_boxes;
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); 
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');
?>