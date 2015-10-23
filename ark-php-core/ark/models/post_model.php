<?php

/**
 * post_model.php
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

class Post_model extends App_Base_Model {

    function listing($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {

        $filter_arr = array();

        if (!empty($filter['tag'])) {
            $filter_arr['AND tag_post.tag_id'] = $filter['tag'];
        }

        $from = 'FROM post LEFT JOIN tag_post ON post.id = tag_post.post_id WHERE 1=1 ';

        foreach ($filter_arr as $k => $v) {
            $from .= $k . ' = ? ';
        }

        $sql = 'SELECT COUNT(*) count ' . $from.' GROUP BY post.id';
        $count = @$this->db->query($sql, $filter_arr)->row()->count;
        if (empty($count)) $count = 0;

        $sql = 'SELECT post.* ' . $from.' GROUP BY post.id';

        $result = $this->db->query($sql, $filter_arr)->result_array();
        foreach ($result as &$value) {
            $tags = $this->db->query('SELECT name FROM tag LEFT JOIN tag_post ON tag.id = tag_post.tag_id WHERE tag_post.post_id = ?', array($value['id']))->result_array();
            $tag_str = array();
            foreach ($tags as $tag) {
                $tag_str[] = $tag['name'];
            }

            $value['tags'] = implode(',', $tag_str);
        }

        return $result;
    }

    function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {

        $filter_arr = array();

        if (!empty($filter['tag'])) {
            $filter_arr['AND tag_post.tag_id'] = $filter['tag'];
        }

        $from = 'FROM post LEFT JOIN tag_post ON post.id = tag_post.post_id WHERE 1=1 ';

        foreach ($filter_arr as $k => $v) {
            $from .= $k . ' = ? ';
        }

        $sql = 'SELECT COUNT(*) ' . $from;
        $count = $this->db->query($sql, $filter_arr)->result_array();

        $sql = 'SELECT post.*, tag_post.tag_id ' . $from;
        $result = $this->db->query($sql, $filter_arr)->result_array();
        foreach ($result as &$value) {
            $tags = $this->db->query('SELECT name FROM tag LEFT JOIN tag_post ON tag.id = tag_post.tag_id WHERE tag_post.post_id = ?', array($value['id']))->result_array();
            $tag_str = '';
            foreach ($tags as $tag) {
                $tag_str .= $tag['name'];
            }

            $value['tags'] = $tag_str;
        }

        return $result;
    }

    function find_by_tag($tag) {
        $sql = 'SELECT p.* FROM tag_post tp
            JOIN tag t ON tp.tag_id = t.id
            JOIN post p ON tp.post_id = p.id
            WHERE t.name = ?
            ORDER BY p.created_time DESC';
        return $this->db->query($sql, array($tag))->result_array();
    }

        function get_tag($id) {
        $this->db->select('tag.*');
        $this->db->join('tag_' . $this->_name, 'tag_' . $this->_name . '.tag_id = tag.id');
        $this->db->where($this->_name . '_id', $id);
        $result = $this->db->get('tag')->result_array();

        $tags = array();
        foreach ($result as $r) {
            $tags[] = $r['name'];
        }

        return $tags;
    }

    function user_tag($user_id = '') {
        $CI = &get_instance();
        if (empty($user_id)) {
            $user = $CI->auth->get_user();
            $user_id = $user['id'];
        }
        $this->db->select('tag.*');
        $this->db->join('tag_' . $this->_name, 'tag_' . $this->_name . '.tag_id = tag.id');
        return $this->db->get('tag')->result_array();
    }

    function update_tag($tags, $id) {
        $CI = &get_instance();
        if (!is_array($tags)) {
            $tags = array($tags);
        }

        $this->db->where(array($this->_name . '_id' => $id));
        $this->db->delete('tag_' . $this->_name);

        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $tag_id = $CI->_model('tag')->add($tag);

                if (!empty($tag_id)) {

                    $obj_tag = array(
                        'tag_id' => $tag_id,
                        $this->_name . '_id' => $id,
                    );

                    Base_Model::before_save($obj_tag);
                    $this->db->insert('tag_' . $this->_name, $obj_tag);
                }
            }
        }
    }

    function add_tag($id, $tags) {
        if (empty($tags)) {
            return;
        }

        if (!is_array($tags)) {
            $tags = array($tags);
        }
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (empty($tag)) {
                continue;
            }

            $row = $this->_db()->query('SELECT * FROM tag WHERE name = ?', array($tag))->row_array();
            if (empty($row)) {
                $data = array('name' => $tag);
                self::before_save($data);
                $this->_db()->insert('tag', $data);
                $tag_id = $this->_db()->insert_id();
            } else {
                $tag_id = $row['id'];
            }

            $data = array(
                'tag_id' => $tag_id,
                'ref_id' => $id,
            );
            self::before_save($data);
            $this->_db()->insert('tag_' . $this->_name, $data);
        }
    }

}

