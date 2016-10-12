<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Oneword Controller
 *
 * @property \App\Model\Table\OnewordTable $Oneword
 */
class OnewordController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'order'=>['created' => 'DESC']
        ];
        $this->set('oneword', $this->paginate($this->Oneword));
        $this->set('_serialize', ['oneword']);
    }

    /**
     * View method
     *
     * @param string|null $id Oneword id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $oneword = $this->Oneword->get($id, [
            'contain' => ['Users']
        ]);
        $this->set('oneword', $oneword);
        $this->set('_serialize', ['oneword']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $oneword = $this->Oneword->newEntity();
        if ($this->request->is('post')) {
            $weather_const = ["任意天气","晴","多云","阴","阵雨","雷阵雨","雷阵雨伴有冰雹","雨夹雪","小雨","中雨","大雨",
               "暴雨","大暴雨","特大暴雨","阵雪","小雪","中雪","大雪","暴雪","雾","沙尘暴","浮尘",
               "扬沙","强沙尘暴","特强沙尘暴轻雾","浓雾","强浓雾","轻微霾","轻度霾","中度霾","重度霾",
               "特强霾","霰"];;
            $datapost = $this->request->data;
            $arr = $this->request->data;
            $wordlist = $this->request->data['wordss'];
            
            $arr['user_id'] = $this->request->session()->read('babe.user');
            $flag_this_is_not_the_first = false;
            $flag_save_all_success = true;

            $word_arr = explode("\n", $wordlist);
            
            foreach ($word_arr as $idx => $word_one) {
                $arr['word'] = $word_one;
                foreach ($datapost['weather'] as $weather_idx => $weather_choose_idx) {
                    $arr['weather'] = $weather_const[$weather_choose_idx];
                
                    if ($flag_this_is_not_the_first) {
                        $oneword = $this->Oneword->newEntity();
                    }
                    $oneword = $this->Oneword->patchEntity($oneword, $arr);
                    if ($this->Oneword->save($oneword)) {
                        $flag_this_is_not_the_first = true;
                        if($idx == count($word_arr) -1)
                        {
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
        $users = $this->Oneword->Users->find('list', ['limit' => 200]);
        $this->set(compact('oneword', 'users'));
        $this->set('_serialize', ['oneword']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Oneword id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $oneword = $this->Oneword->get($id, [
            'contain' => []
        ]);
        

        if ($this->request->is(['patch', 'post', 'put'])) {
            $weather_const = ["任意天气","晴","多云","阴","阵雨","雷阵雨","雷阵雨伴有冰雹","雨夹雪","小雨","中雨","大雨",
               "暴雨","大暴雨","特大暴雨","阵雪","小雪","中雪","大雪","暴雪","雾","沙尘暴","浮尘",
               "扬沙","强沙尘暴","特强沙尘暴轻雾","浓雾","强浓雾","轻微霾","轻度霾","中度霾","重度霾",
               "特强霾","霰"];
            $this->request->data['weather'] = $weather_const[$this->request->data['weather']];
            $oneword = $this->Oneword->patchEntity($oneword, $this->request->data);
            if ($this->Oneword->save($oneword)) {
                $this->Flash->success('句子编辑成功！');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('句子编辑失败，请重试。');
            }
        }
        $users = $this->Oneword->Users->find('list', ['limit' => 200]);
        $this->set(compact('oneword', 'users'));
        $this->set('_serialize', ['oneword']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Oneword id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $oneword = $this->Oneword->get($id);
        if ($this->Oneword->delete($oneword)) {
            $this->Flash->success('The oneword has been deleted.');
        } else {
            $this->Flash->error('The oneword could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
