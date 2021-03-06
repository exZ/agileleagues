<style type="text/css">
	td {
		height: 65px;
		vertical-align: middle !important;
	}

</style>
<div class="panel panel-primary panel-table">
    <div class="panel-heading">
        <div class="panel-title">
            <h1>Players</h1>
        </div>
    </div>
	<?if (empty($players)):?>
	    <div class="panel-body">
    		<p>No players found :(</p>
    		<a class="btn btn-lg btn-success" href="<?= $this->Html->url('/players/invite')?>">Invite some!</a>
    	</div>
	<?else:?>
	    <div class="panel-body">
			<p><?= __('You can reassign players to other teams by clicking on the Team button.')?></p>
	    </div>
	    <div class="panel-body with-table">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th style="text-align: center"><strong>Avatar</strong></th>
					<th><strong>Name</strong></th>
					<th><strong>Team</strong></th>
					<th><strong>E-mail</strong></th>
					<th><strong>Total XP</strong></th>
					<th><strong>Activities Logged</strong></th>
					<th><strong>Progress</strong></th>
					<th><strong>Actions</strong></th>
				</tr>
				<? foreach ($players as $player) : ?>
					<tr>
						<td style="text-align: center"><img src="<? echo $this->Gravatar->get($player['Player']['email'], 60) ?>" alt="" class="img-rounded" width="60"></td>
						<td><? echo h($player['Player']['name']); ?>, <? echo h($player['Player']['title']); ?></td>
						<td><? echo h($player['Team']['name']); ?></td>
						<td><? echo h($player['Player']['email']); ?></td>
						<td><? echo h($player['Player']['xp']); ?></td>
						<td><? echo h($player['Player']['activities']); ?></td>
						<td style="text-align: center">
							<span><? echo (int)$player['Player']['progress']?>%</span>
							
							<div class="progress">
								<div style="width: <? echo (int)$player['Player']['progress']?>%" 
								class="progress-bar progress-bar-info"></div>
							</div>
							<? echo h($player['Player']['level']); ?>
							<i class="glyphicon glyphicon-arrow-right"></i>
							<? echo h(1+$player['Player']['level']); ?>
						</td>
						<td>
							<a class="btn btn-primary" href="<? echo $this->Html->url('/activities/calendar/' . $player['Player']['id']); ?>">
								<i class="entypo-calendar"></i> Activities
							</a>
							<?if ($isGameMaster): ?>
								<a title="<?= __('Change team')?>" class="btn btn-info" href="<? echo $this->Html->url('/players/team/' . $player['Player']['id']); ?>">
									<i class="entypo-users"></i> Team
								</a>
							<?endif;?>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	<?endif;?>
</div>		