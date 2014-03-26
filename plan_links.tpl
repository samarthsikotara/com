<div id="make-plan-wrapper">

    <table style="...">

        Plans:

        {foreach from=$results item=records}

            <tr style="...">


                <td style="...">



                        <a href="{$plan_url}">{$records.link_title}</a>


                </td>
            </tr>
        {/foreach}


    </table>


</div>