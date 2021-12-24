<?php
if (file_exists("functions.php")) {
    require_once("functions.php");
}

if (file_exists("access.php")) {
    require_once("access.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sprint Reporter</title>
	<link rel="stylesheet" href="styles.css?3">
</head>
<body>
		<div class="wrapper">
		<header>
			<div class="header__logo">
				<div class="logo">
					<a href="/" class="logo"></a>
				</div>
			</div>
		</header>
		<main>
			<div class="container">
				<h1>Sprint Report from Jira</h1>
				<?php
					/* Формирование данных из спринта */
						$arFilterData = getJiraFilter('10501');
						$linkSprint = getFilterLink($arFilterData);
						$arSprintInfo = getSprintTasks($linkSprint);
						//getJiraResultTasks($arSprintInfo, false, true); // debug
					/* Формирование данных из спринта */

					$arSprintTasks = getJiraResultTasks($arSprintInfo, 0, 0);
					?>
                    <div class="jreport">
                        <div class="jreport__block clearfix">
                        <table id="jira-employers" class="table table--left" style="height: auto;">
                            <thead>
                                <th>Сотрудник</th>
                                <th>Количество задач</th>
                                <th>Суммарное время (ч)</th>
                                <th>Суммарное время (мин)</th>
                            </thead>
                            <tbody>
                            <?php
                            $arWorkersTasks = makeArrayByWorkers($arSprintInfo);
                            foreach ($arWorkersTasks as $key => $worker):?>
                                <tr>
                                    <td><?=$worker['name']?></td>
                                    <td><?=$worker['tasks_count']?></td>
                                    <td <?php if($worker['sum_hours'] > 28):?>class="table--red_cell"<?php endif;?>>
                                        <?=$worker['sum_hours']?>

                                    </td>
                                    <td><?=$worker['sum_minutes']?></td>
                                </tr>

                            <?php
                            endforeach;
                                ?>
                            </tbody>
                        </table>

                        <table id="jira-components" class="table table--right" style="height: auto;">
                            <thead>
                                <th>Компонент</th>
                                <th>Количество задач</th>
                                <th>Суммарное время (ч)</th>
                            </thead>
                            <tbody>
                            <?php
                            $arComponentsTasks = makeArrayByComponents($arSprintInfo);
                            foreach ($arComponentsTasks as $key => $componentTask):?>
                                <tr>
                                    <td><?=$componentTask['name']?></td>
                                    <td><?=$componentTask['tasks_count']?></td>
                                    <td <?php if($componentTask['sum_hours'] > 28):?>class="table--red_cell"<?php endif;?>>
                                        <?=$componentTask['sum_hours']?>
                                    </td>
                                </tr>

                            <?php
                            endforeach;
                                ?>
                            </tbody>
                        </table>
                        </div> <!--jreport__block-->
                        <div class="jreport__block">
                            <table id="jira-main" class="table" style="height: auto;">
                                <thead>
                                    <th>№</th>
                                    <th>Название</th>
                                    <th>Компонент</th>
                                    <th>Статус</th>
                                    <th>Ответственный</th>
                                    <th>Время (ч)</th>
                                    <th>Автор</th>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($arSprintTasks as $key => $task):
					$classStatus = '';
                                        if($task['fields']['status']['name'] == 'Готово' || $task['fields']['status']['name'] == 'Принято') {
                                            $classStatus = 'table--green_cell';
                                        } else if($task['fields']['status']['name'] == 'В работе') {
                                            $classStatus = 'table--yellow_cell';
                                        }
                                    ?>
                                    <tr>
                                        <td><a href="http://jira:8080/browse/WEB-886" target="_blank"><?=$task['key']?></a></td>
                                        <td><?=$task['fields']['summary']?></td>
                                        <td><?=$task['fields']['components'][0]['name']?></td>
                                        <td class="<?=$classStatus?>"><?=$task['fields']['status']['name']?></td>
                                        <td><?=$task['fields']['assignee']['name']?></td>
                                        <td><?=$task['fields']['aggregatetimeestimate']/60/60?></td>
                                        <td><?=$task['fields']['creator']['name']?></td>
                                    </tr>
                                    <?php
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div> <!--jreport__block-->
                    </div>
			</div>
		</main>
	</div> <!-- wrapper -->
	<div class="foot"></div>
	<footer>

	</footer>


</body>
</html>
