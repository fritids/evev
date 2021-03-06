<?php
function form_for_comments($node) {
    $output = form_open('comments/save');
    $output .= form_hidden('comment[node_id]', $node->id);
    $output .= form_textarea('comment[content]', '', 'class="comments-textarea" placeholder="Agregue su comentario aquí"');
    $output .= form_submit('', 'Guardar comentario', 'class="comment-submit"');
    $output .= form_close();
    return $output;
}

function get_comments($node) {
    $ci =& get_instance();
    $ci->db->where('node_id', $node->id);
    return $ci->db->get('comments')->result();
}

function save_comment($comment) {
    $comment['user_id'] = get_logged_user()->id;
    $comment['created'] = time();
    $ci =& get_instance();
    $ci->db->insert('comments', $comment);
}