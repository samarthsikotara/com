<div id="make-plan-wrapper">

    <table style="...">

        Brands:

        {foreach from=$result item=records}

            <tr style="...">


                <td style="...">



                    <a href="{$records.url}">{$records.name}</a>



                </td>
            </tr>
        {/foreach}


    </table>


</div>