<?php

namespace App\Http\Controllers;

use App\Models\FetchQuest\FetchQuest;
use App\Models\FetchQuest\UserQuest;
use App\Services\FetchQuestManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FetchQuestController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Fetch Quest Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing/completion of fetch quests.
    |
    */

    /**********************************************************************************************

        FETCH QUESTS

    **********************************************************************************************/

    /**
     * Shows the fetch quest index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request) {
        return view('fetch_quests.index', [
            'quests' => FetchQuest::where('is_active', 1)->get(),
        ]);
    }

    /**
     * Shows the fetch quest.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFetchQuest(Request $request) {
        $quest = FetchQuest::where('id', $request->id)->where('is_active', 1)->first();
        if (!$quest) {
            abort(404);
        }

        return view('fetch_quests.fetch_quest', [
            'quest'  => $quest,
            'activeUserQuest' => $quest->activeUserQuest,
            'quests' => FetchQuest::where('is_active', 1)->get(['name', 'id']),
        ]);
    }

    /**
     * Accepts the fetch quest.
     *
     * @param App\Services\FetchQuestManager $manager
     * @param int|null                       $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postAcceptFetchQuest(Request $request, FetchQuestManager $manager, $id) {
        if ($id && $manager->acceptFetchQuest($quest = FetchQuest::find($id), Auth::user())) {
            flash('Fetch quest accepted successfully.')->success();

            return redirect()->to('fetch-quests/'.$id);
        } else {
            foreach ($manager->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Completes the fetch quest.
     *
     * @param App\Services\FetchQuestManager $manager
     * @param int|null                       $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postCompleteFetchQuest(Request $request, FetchQuestManager $manager, $id) {
        if ($id && $manager->completeFetchQuest($quest = FetchQuest::find($id), Auth::user())) {
            flash('Fetch quest completed successfully. Received 1x '.$quest->rewardItem->name.'.')->success();

            return redirect()->to('fetch-quests/'.$id);
        } else {
            foreach ($manager->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
    
    /**
     * Abandons the fetch quest.
     *
     * @param App\Services\FetchQuestManager $manager
     * @param int|null                       $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postAbandonFetchQuest(Request $request, FetchQuestManager $manager, $id) {
        if ($id && $manager->abandonFetchQuest($quest = FetchQuest::find($id), Auth::user())) {
            flash('Fetch quest abandoned successfully.')->success();

            return redirect()->to('fetch-quests/'.$id);
        } else {
            foreach ($manager->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
