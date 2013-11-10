
<nav>
    <div id="menu-separation-haut"></div>
    <div id='cssmenu' style="clear:both;">
        <ul>
            {foreach key=key item=menu from=$sub_menu}
                <li><a href='/?c&id={$menu.id_category}'><span>{$menu.name}</span></a></li>
            {/foreach}
        </ul>
    </div>
    <div id="menu-separation-bas"></div>
</nav>