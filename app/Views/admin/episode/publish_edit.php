<?= $this->extend('admin/_layout') ?>

<?= $this->section('title') ?>
<?= lang('Episode.publish') ?>
<?= $this->endSection() ?>

<?= $this->section('pageTitle') ?>
<?= lang('Episode.publish') ?>
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<?= form_open(route_to('episode-publish_edit', $podcast->id, $episode->id), [
    'method' => 'post',
    'class' => 'flex flex-col max-w-xl items-start',
]) ?>
<?= csrf_field() ?>
<?= form_hidden('client_timezone', 'UTC') ?>
<?= form_hidden('note_id', $note->id) ?>


<label for="message" class="text-lg font-semibold"><?= lang(
    'Episode.publish_form.note',
) . hint_tooltip(lang('Episode.publish_form.note_hint'), 'ml-1') ?></label>
<div class="mb-8 overflow-hidden bg-white shadow-md rounded-xl">
    <div class="flex px-4 py-3">
    <img src="<?= $podcast->actor->avatar_image_url ?>" alt="<?= $podcast->actor
    ->display_name ?>" class="w-12 h-12 mr-4 rounded-full"/>
        <div class="flex flex-col min-w-0">
            <p class="flex items-baseline min-w-0">
                <span class="mr-2 font-semibold truncate"><?= $podcast->actor
                    ->display_name ?></span>
                <span class="text-sm text-gray-500 truncate">@<?= $podcast
                    ->actor->username ?></span>
            </p>
            <time class="text-xs text-gray-500" itemprop="published" datetime="<?= $note->published_at->format(
                DateTime::ATOM,
            ) ?>" title="<?= $note->published_at ?>"><?= lang(
    'Common.mediumDate',
    [$note->published_at],
) ?></time>
        </div>
    </div>
    <div class="px-4 mb-2">
        <?= form_textarea(
            [
                'id' => 'message',
                'name' => 'message',
                'class' => 'form-textarea',
                'required' => 'required',
                'placeholder' => 'Write your message...',
            ],
            old('message', $note->message, false),
            ['rows' => 2],
        ) ?>
    </div>
    <div class="flex">
        <img src="<?= $episode->image
            ->thumbnail_url ?>" alt="<?= $episode->title ?>" class="w-24 h-24" />
        <div class="flex flex-col flex-1">
            <a href="<?= $episode->link ?>" class="flex-1 px-4 py-2 bg-gray-100">
                <div class="flex items-baseline">
                    <span class="flex-1 w-0 mr-2 text-sm font-semibold truncate"><?= $episode->title ?></span>
                    <?= episode_numbering(
                        $episode->number,
                        $episode->season_number,
                        'text-xs font-semibold text-gray-600',
                        true,
                    ) ?>
                </div>
                <div class="text-xs text-gray-600">
                    <time itemprop="published" datetime="<?= $episode->published_at->format(
                        DateTime::ATOM,
                    ) ?>" title="<?= $episode->published_at ?>">
                        <?= lang('Common.mediumDate', [
                            $episode->published_at,
                        ]) ?>
                    </time>
                    <span class="mx-1">•</span>
                    <time datetime="PT<?= $episode->enclosure_duration ?>S">
                        <?= format_duration($episode->enclosure_duration) ?>
                    </time>
                </div>
            </a>
            <audio controls preload="none" class="w-full mt-auto">
                <source src="<?= $episode->enclosure_web_url ?>" type="<?= $episode->enclosure_mimetype ?>">
                Your browser does not support the audio tag.
            </audio>
        </div>
    </div>
    <footer class="flex justify-around px-6 py-3">
        <span class="inline-flex items-center"><?= icon(
            'chat',
            'text-xl mr-1 text-gray-400',
        ) . '0' ?></span>
        <span class="inline-flex items-center"><?= icon(
            'repeat',
            'text-xl mr-1 text-gray-400',
        ) . '0' ?></span>
        <span class="inline-flex items-center"><?= icon(
            'heart',
            'text-xl mr-1 text-gray-400',
        ) . '0' ?></span>
    </footer>
</div>

<?= form_fieldset('', ['class' => 'flex flex-col mb-4']) ?>
<legend class="text-lg font-semibold"><?= lang(
    'Episode.publish_form.publication_date',
) ?></legend>
<label for="now" class="inline-flex items-center">
    <?= form_radio(
        [
            'id' => 'now',
            'name' => 'publication_method',
            'class' => 'text-pine-700',
        ],
        'now',
        old('publication_method') ? old('publish') === 'now' : false,
    ) ?>
    <span class="ml-2"><?= lang(
        'Episode.publish_form.publication_method.now',
    ) ?></span>
</label>
<div class="inline-flex flex-wrap items-center mb-4 radio-toggler">
    <?= form_radio(
        [
            'id' => 'schedule',
            'name' => 'publication_method',
            'class' => 'text-pine-700',
        ],
        'schedule',
        old('publication_method')
            ? old('publication_method') === 'schedule'
            : true,
    ) ?>
    <label for="schedule" class="ml-2"><?= lang(
        'Episode.publish_form.publication_method.schedule',
    ) ?></label>
    <div class="w-full mt-2 radio-toggler-element">
        <?= form_label(
            lang('Episode.publish_form.scheduled_publication_date'),
            'scheduled_publication_date',
            [],
            lang('Episode.publish_form.scheduled_publication_date_hint'),
        ) ?>
        <div class="flex mb-4" data-picker="datetime">
            <?= form_input([
                'id' => 'scheduled_publication_date',
                'name' => 'scheduled_publication_date',
                'class' => 'form-input rounded-r-none flex-1',
                'value' => old(
                    'scheduled_publication_date',
                    $episode->published_at,
                ),
                'data-input' => '',
            ]) ?>
            <button class="p-3 border border-l-0 border-gray-500 bg-pine-100 focus:outline-none rounded-r-md hover:bg-pine-200 focus:ring" type="button" aria-label="<?= lang(
                'Episode.publish_form.scheduled_publication_date_clear',
            ) ?>" title="<?= lang(
    'Episode.publish_form.scheduled_publication_date_clear',
) ?>" data-clear=""><?= icon('close') ?></button>
        </div>
    </div>
</div>
<?= form_fieldset_close() ?>

<div class="self-end">
    <?= anchor(
        route_to('episode-view', $podcast->id, $episode->id),
        lang('Common.cancel'),
        ['class' => 'font-semibold mr-4'],
    ) ?>

    <?= button(
        lang('Episode.publish_form.submit_edit'),
        null,
        ['variant' => 'primary'],
        ['type' => 'submit'],
    ) ?>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>