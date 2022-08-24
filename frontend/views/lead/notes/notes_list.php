<?php

foreach ($notes as $model) {
    echo $this->render('_list_notes', ['model' => $model]);
}
