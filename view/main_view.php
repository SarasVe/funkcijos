<form class='main-nav' method="post">

      <input type="submit" value="products"/>

      <input type="submit" value="analytics"/>

</form>

<div class='info'>

    <?php

    if ($_SERVER['REQUEST_METHOD'] == "POST" and (isset($_POST['action']))) {

        echo " post method passed to script";

        switch ($_POST['action']) {

            case 'products':

                products();

                break;

            case 'select':

                analytics();

                break;
        }
    }
    ?>
    
</div>
