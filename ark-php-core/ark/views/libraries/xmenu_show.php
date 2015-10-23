<ul class="menu <?php echo ($top) ? 'nav' : 'dropdown-menu' ?>">
    <?php foreach ($menus as $menu): ?>
    <li <?php echo (empty($menu['children'])) ? '' : ' class="has-children dropdown" ' ?>>
        <?php $href = (!empty($menu['uri'])) ? ($menu['uri'] === '/') ? base_url() : site_url($menu['uri']) : site_url(@$menu['children'][0]['uri']) ?>
        <?php $data_class = preg_replace('/[ _\/]+/', '-', strtolower($menu['title'])) ?>
        <a href="<?php echo $href ?>" data-class="<?php echo $data_class ?>" <?php echo (!empty($menu['children'])) ? 'data-toggle="dropdown"' : '' ?>>
            <?php echo l($menu['title']) ?>
            <?php if (!empty($menu['children'])): ?>
                <b class="caret"></b>
            <?php endif ?>
        </a>
        <?php if (!empty($menu['children'])): ?>
            <?php echo $self->_get_menu($menu['children']); ?>
        <?php endif ?>
    </li>
    <?php endforeach ?>
</ul>