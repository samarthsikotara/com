{literal}
    <style type="text/css">
        .brand_interests{
            margin-top: 122px;
        }
        #make-brand-wrapper {
        background-color: #f9f9f9;
        padding: 10px;
        margin-top: 30px;
        width: 100%;
        float: left;
        background-repeat: repeat-y;
        margin-bottom: 20px;
        }
        .header_text {
            font-family: "Open Sans";
            float: left;
            width: 550px;
            font-size: 24px;
            color: #444;
            text-align: center;
            height: 40px;
            padding-top: 2px;
            font-weight: 300;
            margin-left: 152px;

        }
        td{


        }


    </style>
{/literal}
{literal}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
    </script>
    <script>
        $(document).ready(function(){
            $("a").click(function(){
                $("textarea").slideToggle();
            });
        });
    </script>
{/literal}
<div id="make-plan-wrapper">
    <div class="registration-button-wrapper registration-button-wrapper-right">
        <div class="register-button next" id="step-2-next" onclick="goToStep(2, 'make-plan-step')"></div>
    </div>
<div class="brand_interests">
    <div id="panel1">
        <form method="POST" action="brand.php">


        <div class="header_text">Brand</div>
        <table style="margin-left: 94px;">

        {foreach from=$interests item=record}
            <tr>
                <td style="width: 150px;">
                    <div class="interest_name">
                        <div class="interest_name">

                        {$record.name}
                        </div>

                    </div>
                </td>
                   {foreach from=$record.type item=type}
                    <td style="width: 150px;">
                       {* {assign var=plan_type value=`$type`}

                        {if in_array({$smarty.request.$plan_type}, $type)}
                         checked
                        *}


                                <input type="checkbox" name="plan_type[{$record.id}][]" value="{$type}"{if !empty($checkboxvalue) && $checkboxvalue == $type}checked="checked"{/if}/>
                                {$type}



                        <div id="comments">
                            <textarea name="comments[{$record.id}][]" cols="7" rows="2" value="comment"></textarea>
                        </div>
                    </td>

                    {/foreach}
                <td style="...">
                    <input type="checkbox" name="shop" value="Shop" />Shop
                    <textarea name="comments[{$record.id}][]" cols="7" rows="2" value="comment"></textarea>
                </td>
            </tr>
        {/foreach}
           </tr>


           <div class="shaukk-buttons universal-red-button home-page-buttons">
            <a herf="#">Add Details</a>
        </table>
        <input type="submit" value="enter" name="submit">


</form>
    </div>
{php}
{/php}
</div>
</div>