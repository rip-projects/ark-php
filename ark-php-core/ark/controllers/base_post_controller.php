<?php

/**
 * post.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

class base_post_controller extends app_crud_controller {

    public function __construct() {
        parent::__construct();

        $this->_validation = array(
            'add' => array(
                array(
                    'field' => 'title',
                    'label' => l('Title'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'post_name',
                    'label' => l('Post Name'),
                    'rules' => 'required|callback__unique_post_name',
                ),
                array(
                    'field' => 'body',
                    'label' => l('Body'),
                    'rules' => 'required',
                ),
            ),
            'edit' => array(
                array(
                    'field' => 'title',
                    'label' => l('Title'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'post_name',
                    'label' => l('Post Name'),
                    'rules' => 'required|callback__unique_post_name',
                ),
                array(
                    'field' => 'body',
                    'label' => l('Body'),
                    'rules' => 'required',
                ),
            ),
        );
    }

    function _unique_post_name($value) {
        $row = $_POST;
        $post = $this->db->query('SELECT * FROM post WHERE post_name LIKE ? AND title != ?', array($value, $row['title']))->row_array();
        if (empty($post)) {
            return true;
        }

        $this->form_validation->set_message('_unique_post_name', 'The %s field must be unique');
        return false;
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('title', 'tags', 'body', 'body', 'post_name');
        $config['formats'] = array('row_detail', '', 'first_thumb', 'plain_limit(50)');
        $config['sorts'] = array(1,1,0,1,1);
        $config['names'] = array('', '', 'Image');
        return $config;
    }

    function _save($id = null) {
        $this->_view = $this->_name . '/show';
        $model = $this->_model();

        if ($_POST) {
            if ($this->_validate()) {
                $_POST['id'] = $id;

                try {
                    $tags = $_POST['tags'];
                    unset($_POST['tags']);
                    $id = $model->save($_POST, $id);

                    $model->update_tag($tags, $id);

                    if (!$this->input->is_ajax_request()) {
                        redirect($this->_get_uri('listing'));
                    }

                    add_info( ($id) ? l('Record updated') : l('Record added') );
                } catch (Exception $e) {
                    add_error(l($e->getMessage()));
                }
            }
        } else {
            if ($id !== null) {
                $this->_data['id'] = $id;
                $_POST = $model->get($id);

                $_POST['tags'] = $model->get_tag($id);
            }
        }
    }

    function view($post_name) {
        $this->_data['post'] = $this->_model()->get(array('post_name' => $post_name));
        if (empty($this->_data['post'])) {
            show_404($this->uri->uri_string);
        }
    }

    function tag($tag) {
        $t = $this->_model('tag')->get(array('name' => $tag));
        $this->_data['tag'] = $tag;
        if (!empty($t)) {
            $this->_data['posts'] = $this->_model()->find(array('tag' => $t['id']));
        } else {
            $this->_data['posts'] = array();
        }
    }

    function listing($offset = 0) {
        parent::listing($offset);

        $tags = $this->_model()->user_tag();

        $this->_data['tag_options'] = array(
            '' => l('(Please select)'),
        );

        foreach ($tags as $tag) {
            $this->_data['tag_options'][$tag['id']] = $tag['name'];
        }
    }

    function _check_access() {
        $allowed = array(
            'view', 'tag',
        );
        foreach ($allowed as $allow) {
            if ($this->uri->rsegments[2] == $allow) {
                return true;
            }
        }
        return parent::_check_access();
    }

}
