
    {foreach key=key item=detail from=$orderdetails}
        <tr>
            <td>{$detail.product_name}</td>
            <td>{$detail.product_width} </td>
            <td>{$detail.product_height} </td>                      
            <td>{$detail.product_quantity}</td>
        </tr>
    {/foreach}