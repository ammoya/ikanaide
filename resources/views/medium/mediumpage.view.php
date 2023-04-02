<div class="querypage_left-column">
    <button id="test">click</button>
    <img src="<?=$mediumInfo['cover']?>" alt="<?=$mediumInfo['title']?>"/>
    <!-- Compruebo si el usuario tiene anime correspondiente a $id en su lista. Si no lo tiene, muestro un botón de añadir; si lo tiene, muestro uno de borrar. -->
    <?php

    if (isset($user_id)) {
        ?>
        <div class="querypage_left-column_user">
        <div class="querypage_left-column_user-list">
        <form action="/add" method="post">
        
        <?php

        if ($result -> num_rows === 1) {
            ?><input class="list-submit box submit-button__colorful" type="submit" value="Delete from list" name="delete"><?php
            if (isset($favOrNot) && $favOrNot === 0) {
                ?><input class="list-submit box submit-button__colorful" type="submit" value="Favourite" name="favourite"><?php
            } else if (isset($favOrNot) && $favOrNot === 1) {
                ?><input class="list-submit box submit-button__colorful" type="submit" value="Unfavourite" name="unfavourite"><?php
            }
        } else {
            ?><input class="list-submit box submit-button__colorful" type="submit" value="Add to list" name="add"><?php
        }

        ?>
        
        </form>
        </div>
        </div>
        
        <?php
    }

    ?>
            
    <section class="querypage_info box-wrapper">
        <div class="box-title">
            <h3>Information</h3>
        </div>
        <div class="box-body">
            <ul class="two-column-list">
                <?php

                switch($medium) {
                    case 'anime':
                        $columns = ['type', 'episodes', 'status', 'start_date', 'end_date'];
                        break;
                    case 'manga':
                        $columns = ['format', 'volumes', 'chapters', 'status', 'start_date', 'end_date'];
                        break;
                    case 'vn':
                        $columns = ['duration', 'released'];
                        break;
                }
                foreach ($columns as $column){
                    ?><li><span class="ul_first-column"><?=str_replace('_', ' ', $column);?></span><span><?=dateFormat($mediumInfo[$column])?></span></li><?php
                }

                ?></ul>
        </div>
    </section>
    <section class="querypage_people two-column-list box-wrapper">
        <div class="box-title">
            <h3>Community</h3>
        </div>
        <div class="box-body">
            <ul class="two-column-list">
                <li><span class="ul_first-column">members</span><span><?=$members?></span></li>
                <li><span class="ul_first-column">loved</span><span><?=$favourites?></span></li>
                <li><span class="ul_first-column">ranked</span><span></span></li>
                <li><span class="ul_first-column">popularity</span><span></span></li>
            </ul>
        </div>
    </section>
    <section class="querypage_edit box-wrapper box-body">
        <!-- broken -->
        <a href="../edit"><p>Edit this page </p></a>
    </section>
</div>

<div class="querypage_right-column">
    <?php
    if ($mediumInfo['header'] !== null) {
        print "<img src=".$mediumInfo['header']." alt=".$mediumInfo['title'].">";
    }
    ?>
    <section class="querypage_title"><h1><?=$mediumInfo['title']?></h1></section>
    <section class="querypage_desc box-wrapper">
        <div class="box-title">
            <h3>Description</h3>
        </div>
        <div class="box-body">
            <?=$mediumInfo['description']?>
        </div>
    </section>
    <section class="querypage_char box-wrapper">
        <div class="querypage_title box-title">
            <h3>Characters</h3><span class="view-all">view all</span>
        </div>
        <div class="querypage_char-entry_wrapper querypage_bg">
            <?php
            if (isset($characters)) {
                for ($i=0; $i<count($characters); $i++) {
                    ?>

                    <div class="querypage_char-entry">
                        <div class="querypage_char-entry_pic">
                            <img src="<?=$characters[$i]['picture']?>" alt="<?=$characters[$i]['family_name'] . ' ' . $characters[$i]['given_name']?>">
                        </div>
                        <div class="querypage_char-entry_info">
                            <div class="querypage_char-entry_info-name">
                                <?=$characters[$i]['family_name'] . ' ' . $characters[$i]['given_name']?>
                            </div>
                            <div class="querypage_char-entry_info-role">
                                <?=strtolower($characters[$i]['role']) . " character"?>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                print "<i style='opacity: var(--font_low-opacity)'>No characters yet...</i>";
            }
            ?>
        </div>
    </section>
    <section class="querypage_staff box-wrapper">
        <div class="box-title">
            <div class="querypage_title"><h3>Staff</h3><span class="view-all">view all</span></div>
        </div>
        <div class="querypage_staff-entry_wrapper querypage_bg">
            <?php
            if (isset($staff)) {
                for ($i=0; $i<count($staff); $i++) {
                    ?>

                    <div class="querypage_staff-entry">
                        <div class="querypage_staff-entry_pic">
                            <img src="<?=$staff[$i]['picture']?>" alt="<?=$staff[$i]['family_name'] . ' ' . $staff[$i]['given_name']?>">
                        </div>
                        <div class="querypage_staff-entry_info">
                            <div class="querypage_staff-entry_info-name">
                                <?=$staff[$i]['family_name'] . ' ' . $staff[$i]['given_name']?>
                            </div>
                            <div class="querypage_staff-entry_info-role">
                                <?=strtolower($staff[$i]['role'])?>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                print "<i style='opacity: var(--font_low-opacity)'>No staff yet...</i>";
            }
            ?>
        </div>
    </section>
    <section class="querypage_review box-wrapper">
        <div class="querypage_title box-title"><h3>Reviews</h3><span class="view-all">view all</span></div>
        <div class="querypage_review-entry_wrapper querypage_bg">
            <?php
            if (isset($reviews)) {
                for ($i=0; $i<count($reviews); $i++) {
                    ?>

                    <div class="querypage_review-entry">
                        <div class="querypage_review-entry_pic">
                            <img src="<?=$mediumInfo['cover']?>" alt="<?=$mediumInfo['title']?>">
                        </div>
                        <div class="querypage_review-entry_info">
                            <div class="querypage_review-entry_info-name">
                                <i><?=$reviews[$i]['title']?></i>
                            </div>
                            <div class="querypage_review-entry_info-role">
                                <span>by <a href="/<?=$reviews[$i]['user_id']?>"><?=$reviews[$i]['user_id']?></a>.</span>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                print "<i style='opacity: var(--font_low-opacity); padding: 10px 0'>No reviews yet...</i>";
            }
            ?>
        </div>
    </section>
</div>



<section id="querypage_list-edit">
    <div class="querypage_list-edit_wrapper">
        <div class="querypage_list-edit_header" style="background-image: url(<?=$mediumInfo['header']?>)">
            <img src="<?=$mediumInfo['cover']?>" alt="<?=$mediumInfo['title']?>">
        </div>
        <div class="querypage_list-edit_form">
            <form action="<?='/'.$medium.'/'.$entry?>">
                <div class="form-title">
                    <h3><?=$mediumInfo['title']?></h3>
                </div>
                <div class="form-fields">
                    <input type="text" name="score" id="score">
                    <input type="text" name="progress" id="progress">
                </div>
            </form>
        </div>
    </div>
</section>