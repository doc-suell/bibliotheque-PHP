<?php
require dirname(dirname(__DIR__)) . '/functions.php';
require_once PATH_PROJECT . '/connect.php';
?>

<main class="file_form">
    <form action="<?php echo HTML_URL . "ajax/add_autor.php";?>" method="POST" id="add_author">
    <div>
        <label for="firstname">Prénom <span class="red">*</span></label>
        <input type="text" name="firstname" id="firstname">
    </div>
    <div>
        <label for="lastname">Nom <span class="red">*</span></label>
        <input type="text" name="lastname" id="lastname">
    </div>
    <div>
        <label for="date_author">Date de naissance <span class="red">*</span></label>
        <select name="date" name="date_author"  id="date_author">
    </div>
    <div>
        <label for="country">Sélectionner le pays <span class="red">*</span></label>
        <select name="country"  id="country">
            <?php
            $req = $db->querry("
                SELECT *
                FROM pays");
                while($country = $req->fetch(PDO::FETCH_OBJ)):
                    $selected = $country->code_pays == 'FR' ? 'selected' : "";
                    echo '<option value ="' . $country->id . '"' . $selected . '>' . $country->inititule '</option>'
                    endwhile
                    ?>
        </select>
    </div>
        </form>
