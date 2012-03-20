<h1 class="page-title"><?= $page_title ?></h1>
Idioma del <?= $node->type; ?>: <strong class="language-name"><?= language_name($node->language) ?></strong>
<?php if (is_translation($node_id)): $original = get_original($node_id); ?>
    Esto es una traducción de <?= anchor($original->language.'/nodes/edit/'.$original->id, $original->title) ?>
<?php elseif (has_translation($node->id)): $translations = get_translations($node->id); ?>
        Traducciones: <?php foreach ($translations as $translation): ?>
            <?= anchor($translation->translation_language.'/nodes/edit/'.$translation->translation_id, $translation->translation_language); ?>
        <?php endforeach; ?>
<?php else: ?>
        Traducir en: 
        <?php
            $languages = get_languages();
            foreach ($languages as $language): if ($language->language_code == $node->language) continue; ?>
                <?= anchor($language->language_code.'/nodes/translate/'.$node->id.'/'.$language->language_code, $language->language_name); ?>
            <?php endforeach; ?>
<?php endif; ?>




<?php node_form($node_type, $node); ?>