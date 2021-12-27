<?php

//define("JIRA_URL", "https://jira");
//define("USERNAME", "login");
//define("PASSWORD", "token");

/**
 *
	# /rest/api/2/filter/xxxxxx
	# /rest/api/latest/project - получить список проектов
	# /rest/api/latest/project/WEB - получить информацию о проекте с ключом WEB
	# /rest/api/latest/issue/WEB-13 - получить информацию о задаче WEB-13
	# /rest/api/latest/priority - список возможных приоритетов для задач
	# /rest/api/latest/issue/WEB-13/worklog - список ворклогов для задачи с ключом WEB-13
*/

function getJiraFilter($resource) {
	/*
	 *
	 */
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => JIRA_URL . '/rest/api/2/filter/' . $resource,
		CURLOPT_USERPWD => USERNAME . ':' . PASSWORD,
		CURLOPT_HTTPHEADER => array('Content-type: application/json', 'Authorization: Bearer NDI2Nfg345345dgfpomI0EjoK1ijke57I'),
		CURLOPT_RETURNTRANSFER => true
	));
	$result = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($result, true);
	return $result;

}

function getFilterLink($filterData) {
	/**
	* Функция выделяет из входящего массива фильтра Jira JQL ссылку на массив
	*/
	$link = $filterData['searchUrl'];
	return $link;
}

function getSprintTasks($link) {
	/**
	* Запрос к таскам спринта по JQL ссылке
	*/
	$ch = curl_init();
	//configure CURL
	curl_setopt_array($ch, array(
		CURLOPT_URL => $link,
		CURLOPT_USERPWD => USERNAME . ':' . PASSWORD,
		CURLOPT_HTTPHEADER => array('Content-type: application/json', 'Authorization: Bearer NDI2dgfget34erwe57I'),
		CURLOPT_RETURNTRANSFER => true
	));
	$result = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($result, true);
	return $result;
}

function getJiraResultTasks($tasks, $show = false, $debug = false) {
	/**
	* Перебор задач из массива
	* $show - отображает пример массива
	*/
	$arTasks = $tasks['issues'];
	if($show) {
		foreach ($arTasks as $key => $task) {
			echo $task['key'].' | '.$task['fields']['status']['name'].' | '.$task['fields']['summary'].' | Время: '.$task['fields']['aggregatetimeestimate']/60/60 . '<br>';
		}
	}
	if($debug) {
		echo '<pre>';
		print_r($arTasks);
		echo '</pre>';
	}
	return $arTasks;
}

function makeArrayByWorkers($tasks) {
	/**
	* Отсортированный массив по сотрудникам
	*/
	$arTasks = $tasks['issues'];
	$arWorkers = Array();
	foreach ($arTasks as $key => $task) {

		if(!isset($arWorkers[$task['fields']['assignee']['name']])) {
			$arWorkers[$task['fields']['assignee']['name']]['name'] = $task['fields']['assignee']['name'];
			$arWorkers[$task['fields']['assignee']['name']]['displayName'] = $task['fields']['assignee']['displayName'];
			$arWorkers[$task['fields']['assignee']['name']]['sum_hours'] = 0;
			$arWorkers[$task['fields']['assignee']['name']]['sum_minutes'] = 0;
			$arWorkers[$task['fields']['assignee']['name']]['tasks_count'] = 0;

		}
		$arWorkers[$task['fields']['assignee']['name']]['sum_hours'] = $arWorkers[$task['fields']['assignee']['name']]['sum_hours'] + $task['fields']['aggregatetimeestimate']/60/60;
		$arWorkers[$task['fields']['assignee']['name']]['sum_minutes'] += $task['fields']['aggregatetimeestimate']/60;
		$arWorkers[$task['fields']['assignee']['name']]['tasks_count'] = $arWorkers[$task['fields']['assignee']['name']]['tasks_count'] + 1;

		$arWorkers[$task['fields']['assignee']['name']]['tasks'][$task['key']] = $task['fields'];

	}
	return $arWorkers;
}

function makeArrayByComponents($tasks) {
	/**
	* Отсортированный массив по компонентам
	*/
	$arTasks = $tasks['issues'];
	$arComponents = Array();
	foreach ($arTasks as $key => $task) {

		if(!isset($arComponents[$task['fields']['components'][0]['name']])) {
			$arComponents[$task['fields']['components'][0]['name']]['name'] = $task['fields']['components'][0]['name'];
			$arComponents[$task['fields']['components'][0]['name']]['sum_hours'] = 0;
			$arComponents[$task['fields']['components'][0]['name']]['sum_minutes'] = 0;
			$arComponents[$task['fields']['components'][0]['name']]['tasks_count'] = 0;

		}
		$arComponents[$task['fields']['components'][0]['name']]['sum_hours'] = $arComponents[$task['fields']['components'][0]['name']]['sum_hours'] + $task['fields']['aggregatetimeestimate']/60/60;
		$arComponents[$task['fields']['components'][0]['name']]['sum_minutes'] += $task['fields']['aggregatetimeestimate']/60;
		$arComponents[$task['fields']['components'][0]['name']]['tasks_count'] = $arComponents[$task['fields']['components'][0]['name']]['tasks_count'] + 1;

		$arComponents[$task['fields']['components'][0]['name']]['tasks'][$task['key']] = $task['fields'];

	}
	return $arComponents;
}

?>
