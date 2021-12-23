<?php

declare(strict_types=1);

/**
 * @copyright  2020 Podlibre
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html AGPL3
 * @link       https://castopod.org/
 */

namespace Modules\Admin\Controllers;

use App\Entities\Clip\VideoClip;
use App\Entities\Episode;
use App\Entities\Podcast;
use App\Models\ClipModel;
use App\Models\EpisodeModel;
use App\Models\PodcastModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class VideoClipsController extends BaseController
{
    protected Podcast $podcast;

    protected Episode $episode;

    public function _remap(string $method, string ...$params): mixed
    {
        if (
            ($podcast = (new PodcastModel())->getPodcastById((int) $params[0])) === null
        ) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->podcast = $podcast;

        if (count($params) > 1) {
            if (
                ! ($episode = (new EpisodeModel())
                    ->where([
                        'id' => $params[1],
                        'podcast_id' => $params[0],
                    ])
                    ->first())
            ) {
                throw PageNotFoundException::forPageNotFound();
            }

            $this->episode = $episode;

            unset($params[1]);
            unset($params[0]);
        }

        return $this->{$method}(...$params);
    }

    public function list(): string
    {
        $videoClipsBuilder = (new ClipModel('video'))
            ->where([
                'podcast_id' => $this->podcast->id,
                'episode_id' => $this->episode->id,
                'type' => 'video',
            ])
            ->orderBy('created_at', 'desc');

        $clips = $videoClipsBuilder->paginate(10);

        $videoClips = [];
        foreach ($clips as $clip) {
            $videoClips[] = new VideoClip($clip->toArray());
        }

        $data = [
            'podcast' => $this->podcast,
            'episode' => $this->episode,
            'videoClips' => $videoClips,
            'pager' => $videoClipsBuilder->pager,
        ];

        replace_breadcrumb_params([
            0 => $this->podcast->title,
            1 => $this->episode->title,
        ]);
        return view('episode/video_clips_list', $data);
    }

    public function view($videoClipId): string
    {
        $videoClip = (new ClipModel())->getVideoClipById((int) $videoClipId);

        $data = [
            'podcast' => $this->podcast,
            'episode' => $this->episode,
            'videoClip' => $videoClip,
        ];

        replace_breadcrumb_params([
            0 => $this->podcast->title,
            1 => $this->episode->title,
            2 => $videoClip->label,
        ]);
        return view('episode/video_clip', $data);
    }

    public function generate(): string
    {
        helper('form');

        $data = [
            'podcast' => $this->podcast,
            'episode' => $this->episode,
        ];

        replace_breadcrumb_params([
            0 => $this->podcast->title,
            1 => $this->episode->title,
        ]);
        return view('episode/video_clips_new', $data);
    }

    public function attemptGenerate(): RedirectResponse
    {
        // TODO: add end_time greater than start_time, with minimum ?
        $rules = [
            'start_time' => 'required|numeric',
            'end_time' => 'required|numeric|differs[start_time]',
            'format' => 'required|in_list[' . implode(',', array_keys(config('MediaClipper')->formats)) . ']',
            'theme' => 'required|in_list[' . implode(',', array_keys(config('Colors')->themes)) . ']',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $themeName = $this->request->getPost('theme');
        $themeColors = config('MediaClipper')
            ->themes[$themeName];
        $theme = [
            'name' => $themeName,
            'preview' => $themeColors['preview'],
        ];

        $videoClip = new VideoClip([
            'label' => 'NEW CLIP',
            'start_time' => (float) $this->request->getPost('start_time'),
            'end_time' => (float) $this->request->getPost('end_time',),
            'theme' => $theme,
            'format' => $this->request->getPost('format'),
            'type' => 'video',
            'status' => 'queued',
            'podcast_id' => $this->podcast->id,
            'episode_id' => $this->episode->id,
            'created_by' => user_id(),
            'updated_by' => user_id(),
        ]);

        (new ClipModel())->insert($videoClip);

        return redirect()->route('video-clips-generate', [$this->podcast->id, $this->episode->id])->with(
            'message',
            lang('Settings.images.regenerationSuccess')
        );
    }
}