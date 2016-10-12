<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class GovController extends AppController
{

    /**
     * Displays a view
     *
     * @return void|\Cake\Network\Response
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function home()
    {

        $this->loadModel('City');//
        $this->loadModel('Background');
        $this->loadModel('Figure');
        $this->loadModel('Oneword');
        $this->set('city_count',$this->City->find()->count());
        $this->set('back_count',$this->Background->find()->count());
        $this->set('figure_count',$this->Figure->find()->count());
        $this->set('words_count',$this->Oneword->find()->count());
        // $path = func_get_args();

        // $count = count($path);
        // if (!$count) {
        //     return $this->redirect('/');
        // }
        // $page = $subpage = null;

        // if (!empty($path[0])) {
        //     $page = $path[0];
        // }
        // if (!empty($path[1])) {
        //     $subpage = $path[1];
        // }
        // $this->set(compact('page', 'subpage'));

    }

    public function isAuthorized($user)
    {
        // print_r($user);
        // print_r($this->Auth->identify());
        // print_r($this->Auth->user($user['id']));
        // $this->request->session()->delete('babe.user');
        // print_r($this->request->session()->read('babe.user'));


        // $action = $this->request->params['action'];

        // // The add and index actions are always allowed.
        // if (in_array($action, ['index', 'add', 'tags'])) {
        //     return true;
        // }
        // // All other actions require an id.
        // if (empty($this->request->params['pass'][0])) {
        //     return false;
        // }

        // Check that the bookmark belongs to the current user.
        // $id = $this->request->params['pass'][0];
        // $bookmark = $this->Bookmarks->get($id);
        // if ($bookmark->user_id ==$user['id']) {
        //     return true;
        // }
        return parent::isAuthorized($user);
    }


    public function upload_background()
    {
        $this->layout = false;
        $this->autoRender = false;
        // print_r(APP . 'Model' . DS . 'UploadHandler.php');
        require_once(APP . 'Controller' . DS . 'UploadHandler.php');
        $options = array('script_url'=>'/upload_figure', 'upload_dir' => WWW_ROOT . '/files/background/','upload_url'=>'/files/background/');
        $upload_handler = new UploadHandler($options);
    }
    public function upload_figure()
    {
        $this->layout = false;
        $this->autoRender = false;
        // print_r(APP . 'Model' . DS . 'UploadHandler.php');
        require_once(APP . 'Controller' . DS . 'UploadHandler.php');
        $options = array('script_url'=>'/upload_figure','upload_dir' => WWW_ROOT .'/files/figure/','upload_url'=>'/files/figure/');
        $upload_handler = new UploadHandler($options); 
    }

    public function settings()
    {
        
    }
    public function user_settings()
    {
        
    }
}
