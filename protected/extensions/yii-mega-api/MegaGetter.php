<?php

/**
 * Mega API Getter class file.
 * @author Francesco Colamonici <francesco.colamonici@accenture.com>
 * @copyright Copyright &copy; Francesco Colamonici 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version 1.0.0
 */
Yii::import('mega.MegaHelper', true);

abstract class MegaGetter extends MegaHelper {

    protected function find($filename) {
        $files = $this->get_files();
        foreach ($files as $file) {
            if ($file->a && $file->a->n == $filename && $file->p != parent::$trashbin_id)
                return $file;
        }
        return null;
    }

    protected function get_files() {
        $files_dict = array();
        $files = parent::api_request(array('a' => 'f', 'c' => 1));
        foreach ($files->f as $file) {
            $processed_file = $this->process_file($file);
            if ($processed_file->a)
                $files_dict[$file->h] = $processed_file;
        }
        return $files_dict;
    }

    protected function get_user() {
        $user_data = parent::api_request(array('a' => 'ug'));
        return $user_data;
    }

    /**
     * Get a node by it's numeric type id, e.g:
     *   0: file
     *   1: dir
     *   2: special: root cloud drive
     *   3: special: inbox
     *   4: special: trash bin
     * @param integer $type
     */
    protected function get_node_by_type($type) {
        $nodes = $this->get_files();
        foreach ($nodes as $node) {
            if ($node->t == $type)
                return $node;
        }
    }

    /**
     * Get all files in a given target, e.g. 4=trash
     * @param integer $target
     */
    protected function get_files_in_node($target) {
        $node_id = array();
        if (is_int($target))
            $node_id = $this->get_node_by_type($target);
        else
            $node_id = array($target);
        $files_dict = array();
        $files = parent::api_request(array('a' => 'f', 'c' => 1));
        foreach ($files->f as $file) {
            $processed_file = $this->process_file($file);
            if ($processed_file->a && $processed_file->p == $node_id[0])
                $files_dict[$file->h] = $processed_file;
        }
        return $files_dict;
    }

    protected function get_node_by_id($id) {
        $resp = parent::api_request(array('a' => 'f', 'n' => $id));
        $files = $resp->f;
        return $this->process_file($files[0]);
    }

    protected function get_node_id($node) {
        return $node->h;
    }

}

?>
