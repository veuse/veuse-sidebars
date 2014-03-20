Veuse Sidebars
==============

WordPress plugin for creating unlimited sidebars/widget areas.

The plugin creates a custom post-type for sidebars, that is located under Appearance > Sidebar Generator in the admin. 
It also creates a meta-box for posts and pages for sidebar selection.

It requires you add a little code to your sidebar.php to display the sidebar:

    Code coming here

By default, the sidebar selector is added to pages and posts. If you want to add it to other post-types, you need to add the following code to your themes functions.php

    $sidebar_posttypes = array( 'page', 'post','yourcustomposttypename' ); 
    $sidebar_generator = new Veuse_Custom_Sidebar(  $sidebar_posttypes  );
