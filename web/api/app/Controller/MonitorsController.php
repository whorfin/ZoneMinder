<?php
App::uses('AppController', 'Controller');
/**
 * Monitors Controller
 *
 * @property Monitor $Monitor
 * @property PaginatorComponent $Paginator
 */
class MonitorsController extends AppController {


/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'RequestHandler');

/**
 * index method
 *
 * @return void
 */
	public function index() {
                $this->Monitor->recursive = 0;
        	$monitors = $this->Monitor->find('all');
        	$this->set(array(
        	    'monitors' => $monitors,
        	    '_serialize' => array('monitors')
        	));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Monitor->recursive = 0;
		if (!$this->Monitor->exists($id)) {
			throw new NotFoundException(__('Invalid monitor'));
		}
		$options = array('conditions' => array('Monitor.' . $this->Monitor->primaryKey => $id));
		$monitor = $this->Monitor->find('first', $options);
		$this->set(array(
			'monitor' => $monitor,
			'_serialize' => array('monitor')
		));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Monitor->create();
			if ($this->Monitor->save($this->request->data)) {
				$this->daemonControl($this->Monitor->id, 'start', $this->request->data);
				return $this->flash(__('The monitor has been saved.'), array('action' => 'index'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Monitor->id = $id;

		if (!$this->Monitor->exists($id)) {
			throw new NotFoundException(__('Invalid monitor'));
		}

		if ($this->Monitor->save($this->request->data)) {
			$message = 'Saved';
		} else {
			$message = 'Error';
		}

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Monitor->id = $id;
		if (!$this->Monitor->exists()) {
			throw new NotFoundException(__('Invalid monitor'));
		}
		$this->request->allowMethod('post', 'delete');

		$this->daemonControl($this->Monitor->id, 'stop');

		if ($this->Monitor->delete()) {
			return $this->flash(__('The monitor has been deleted.'), array('action' => 'index'));
		} else {
			return $this->flash(__('The monitor could not be deleted. Please, try again.'), array('action' => 'index'));
		}
	}

	public function sourceTypes() {
		$sourceTypes = $this->Monitor->query("describe Monitors Type;");

		preg_match('/^enum\((.*)\)$/', $sourceTypes[0]['COLUMNS']['Type'], $matches);
		foreach( explode(',', $matches[1]) as $value ) {
			$enum[] = trim( $value, "'" );
		}

		$this->set(array(
			'sourceTypes' => $enum,
			'_serialize' => array('sourceTypes')
		));
	}

	// Check if a daemon is running for the monitor id
	public function daemonStatus() {
		$id = $this->request->params['named']['id'];
		$daemon = $this->request->params['named']['daemon'];

		if (!$this->Monitor->exists($id)) {
			throw new NotFoundException(__('Invalid monitor'));
		}

		$monitor = $this->Monitor->find('first', array(
			'fields' => array('Id', 'Type', 'Device'),
			'conditions' => array('Id' => $id)
		));

		// Clean up the returned array
		$monitor = Set::extract('/Monitor/.', $monitor);

		// Pass -d for local, otherwise -m
		if ($monitor[0]['Type'] == 'Local') {
			$args = "-d ". $monitor[0]['Device'];	
		} else {
			$args = "-m ". $monitor[0]['Id'];
		}

		// Build the command, and execute it
		$zm_path_bin = Configure::read('ZM_PATH_BIN');
		$command = escapeshellcmd("$zm_path_bin/zmdc.pl status $daemon $args");
		$status = exec( $command );

		// If 'not' is present, the daemon is not running, so return false
		// https://github.com/ZoneMinder/ZoneMinder/issues/799#issuecomment-108996075
		// Also sending back the status text so we can check if the monitor is in pending
		// state which means there may be an error
		$statustext = $status;
		$status = (strpos($status, 'not')) ? false : true;

		$this->set(array(
			'status' => $status,
			'statustext' => $statustext,
			'_serialize' => array('status','statustext'),
		));
	}

	public function daemonControl($id, $command, $monitor=null, $daemon=null) {
		$args = '';
		$daemons = array();

		if (!$monitor) {
			// Need to see if it is local or remote
			$monitor = $this->Monitor->find('first', array(
				'fields' => array('Type', 'Function'),
				'conditions' => array('Id' => $id)
			));
			$monitor = $monitor['Monitor'];
		}

		if ($monitor['Type'] == 'Local') {
			$args = "-d " . $monitor['Device'];
		} else {
			$args = "-m " . $id;
		}

		if ($monitor['Function'] == 'Monitor') {
			array_push($daemons, 'zmc');
		} else {
			array_push($daemons, 'zmc', 'zma');
		}
		
		$zm_path_bin = Configure::read('ZM_PATH_BIN');

		foreach ($daemons as $daemon) {
			$shellcmd = escapeshellcmd("$zm_path_bin/zmdc.pl $command $daemon $args");
			$status = exec( $shellcmd );
		}
	}

}

