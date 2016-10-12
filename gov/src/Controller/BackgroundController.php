<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Background Controller
 *
 * @property \App\Model\Table\BackgroundTable $Background
 */
class BackgroundController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],'order'=>['created' => 'DESC']
        ];
        $this->set('background', $this->paginate($this->Background));
        $this->set('_serialize', ['background']);
    }

    /**
     * View method
     *
     * @param string|null $id Background id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $background = $this->Background->get($id, [
            'contain' => ['Users']
        ]);
        $this->set('background', $background);
        $this->set('_serialize', ['background']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $background = $this->Background->newEntity();
        if ($this->request->is('post')) {
            // print_r($this->request->data);
            if (!is_numeric($this->request->data['filecount'])) {
                return $this->Flash->error('您在干什么.');;
            }
            $weather_const = ["任意天气","晴","多云","阴","阵雨","雷阵雨","雷阵雨伴有冰雹","雨夹雪","小雨","中雨","大雨",
               "暴雨","大暴雨","特大暴雨","阵雪","小雪","中雪","大雪","暴雪","雾","沙尘暴","浮尘",
               "扬沙","强沙尘暴","特强沙尘暴轻雾","浓雾","强浓雾","轻微霾","轻度霾","中度霾","重度霾",
               "特强霾","霰"];;
            $file_count = intval($this->request->data['filecount']);
            $datapost = $this->request->data;
            $flag_this_is_not_the_first = false;
            $flag_save_all_success = true;
            for ($idx=0; $idx < $file_count; $idx++) { 
                $arr['name'] = $datapost['name'];
              
                foreach ($datapost['weather'] as $weather_idx => $weather_choose_idx) {
                    $arr['weather'] = $weather_const[$weather_choose_idx];
                    $arr['ge_hour'] = $datapost['ge_hour'];
                    $arr['le_hour'] = $datapost['le_hour'];
                    $arr['ge_week'] = $datapost['ge_week'];
                    $arr['le_week'] = $datapost['le_week'];
                    $arr['ge_month'] = $datapost['ge_month'];
                    $arr['le_month'] = $datapost['le_month'];
                    $arr['ge_temp'] = $datapost['ge_temp'];
                    $arr['le_temp'] = $datapost['le_temp'];
                    $arr['ge_aqi'] = $datapost['ge_aqi'];
                    $arr['le_aqi'] = $datapost['le_aqi'];
                    $arr['reso'] = $datapost['reso'];
                    
                    $idxxx = ($idx+1);
                    $arr['filename'] = $datapost['filename_' . $idxxx];
                    $arr['path'] = $datapost['filepath_' . $idxxx];
                    $arr['md5'] = $datapost['filemd5sum_' . $idxxx];
                    $arr['size'] = $datapost['filesize_' . $idxxx];
                    $arr['user_id'] = $this->request->session()->read('babe.user');
                    if ($flag_this_is_not_the_first) {
                        $background = $this->Background->newEntity();
                    }
                    $background = $this->Background->patchEntity($background, $arr);
                    if ($this->Background->save($background)) {
                        $flag_this_is_not_the_first = true;
                        if ($idx == $file_count - 1) {
                            $flag_save_all_success = true;
                        }
                    } else {
                        $flag_save_all_success = false;
                    }
                }
                
            }
            if ($flag_save_all_success) {
                $this->Flash->success('全部保存成功.');
                return $this->redirect('/');
            }else{
                $this->Flash->error('未能全部成功保存.');
            }
            
        }
        // $users = $this->Background->Users->find('list', ['limit' => 200]);
        $user_id = $this->request->session()->read('babe.user');
        $this->set(compact('background'));
        $this->set('user_id',$user_id);
        $this->set('_serialize', ['background']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Background id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $background = $this->Background->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $background = $this->Background->patchEntity($background, $this->request->data);
            if ($this->Background->save($background)) {
                $this->Flash->success('The background has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The background could not be saved. Please, try again.');
            }
        }
        $users = $this->Background->Users->find('list', ['limit' => 200]);
        $this->set(compact('background', 'users'));
        $this->set('_serialize', ['background']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Background id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $background = $this->Background->get($id);
        if ($this->Background->delete($background)) {
            $this->Flash->success('The background has been deleted.');
        } else {
            $this->Flash->error('The background could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
