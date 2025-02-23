<?php
/**
 * @var \App\Entity\Player $player
 */
?>
<div class="flex bg-white md:h-min-22 h-min-20	 dark:bg-gray-800 rounded-lg shadow mb-5 px-10 py-4">
    <h1 class="text-2xl block">#<?= $player->id; ?> <?= $player->username; ?></h1>
    <?= $player->email; ?>

    <a href="/player/show?id=<?= $player->id; ?>">
        <i class="fas fa-eye"></i>
    </a>
    <a href="/player/edit?id=<?= $player->id; ?>">
        <i class="fas fa-edit"></i>
    </a>
    <a href="/player/delete?id=<?= $player->id; ?>"
       onclick="return confirm('Are you sure you want to delete it?')">
        <i class="fas fa-trash"></i>
    </a>


</div>

<div class="block w-full">
    <h2 class="d-block text-3xl">Owned Games</h2>
    <form method="POST" action="/player/addgame?id=<?= $player->id; ?>">
        <select name="game"
                class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
            <?php foreach ($availableGames as $game): ?>
                <option value="<?= $game->id; ?>"><?= $game->name; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add to
            games
        </button>

    </form>

    <div class="flex gap-2 bg-white md:h-min-22 h-min-20	 dark:bg-gray-800 rounded-lg shadow mb-5 px-10 py-4">

        <?php foreach ($player->getOwnedGames() as $game): ?>
            <div class="flex bg-white md:h-60 h-24	 dark:bg-gray-800 rounded-lg shadow mb-5">
                <div class="flex-none w-24 md:w-60  relative">
                    <img src="<?= $game->image; ?>" class="absolute rounded-lg inset-0 w-full h-full object-cover"/>
                </div>
                <div class="flex-auto p-6">
                    <div class="flex flex-wrap">
                        <h1 class="flex-auto text-xl font-semibold dark:text-gray-50">
                            <?= $game->name; ?>
                        </h1>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>


    </div>
</div>


