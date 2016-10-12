<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * City Controller
 *
 * @property \App\Model\Table\CityTable $City
 */
class CityController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('city', $this->paginate($this->City));
        $this->set('_serialize', ['city']);
    }

    /**
     * View method
     *
     * @param string|null $id City id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $city = $this->City->get($id, [
            'contain' => []
        ]);
        $this->set('city', $city);
        $this->set('_serialize', ['city']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $city = $this->City->newEntity();
        if ($this->request->is('post')) {
            $city = $this->City->patchEntity($city, $this->request->data);
            if ($this->City->save($city)) {
                $this->Flash->success('The city has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The city could not be saved. Please, try again.');
            }
        }
        $this->set(compact('city'));
        $this->set('_serialize', ['city']);
    }

    /**
     * Edit method
     *
     * @param string|null $id City id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $city = $this->City->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $city = $this->City->patchEntity($city, $this->request->data);
            if ($this->City->save($city)) {
                $this->Flash->success('The city has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The city could not be saved. Please, try again.');
            }
        }
        $this->set(compact('city'));
        $this->set('_serialize', ['city']);
    }

    /**
     * Delete method
     *
     * @param string|null $id City id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    // public function delete($id = null)
    // {
    //     $this->request->allowMethod(['post', 'delete']);
    //     $city = $this->City->get($id);
    //     if ($this->City->delete($city)) {
    //         $this->Flash->success('The city has been deleted.');
    //     } else {
    //         $this->Flash->error('The city could not be deleted. Please, try again.');
    //     }
    //     return $this->redirect(['action' => 'index']);
    // }
}
