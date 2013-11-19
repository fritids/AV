
<nav>
    <div id="menu-separation-haut"></div>
    <div id='cssmenu' style="clear:both;">
        <ul>
            {foreach key=key item=menu from=$sub_menu}
                <li><a href='/{$menu.id_category}-{$menu.link_rewrite}'><span><h2 style="font-family: Arial;font-size: 12px;font-weight: bold;display:inline;">{$menu.name}</h2></span></a></li>
            {/foreach}
        </ul>
    </div>
    <div id="menu-separation-bas"></div>
</nav>