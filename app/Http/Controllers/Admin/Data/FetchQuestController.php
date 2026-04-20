<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\FetchQuest\FetchQuest;
use App\Models\Loot\LootTable;
use App\Services\FetchQuestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FetchQuestController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Fetch Quest Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of fetch quests.
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
        $quests = FetchQuest::query();
        if ($request->has('is_active')) {
            $quests->where('is_active', $request->get('is_active'));
        }

        return view('admin.fetch_quests.index', [
            'quests' => $quests->get(),
        ]);
    }

    /**
     * Shows the create quest page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFetchQuest() {
        return view('admin.fetch_quests.create_edit_fetch_quest', [
            'quest'       => new FetchQuest,
            'is_active'   => [1, 2],
            'lootTables'  => LootTable::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Shows the edit quest page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFetchQuest($id) {
        $quest = FetchQuest::find($id);
        if (!$quest) {
            abort(404);
        }

        return view('admin.fetch_quests.create_edit_fetch_quest', [
            'quest'       => $quest,
            'is_active'   => [1, 2],
            'lootTables'  => LootTable::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits a fetch quest.
     *
     * @param App\Services\FetchQuestService $service
     * @param int|null                       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFetchQuest(Request $request, FetchQuestService $service, $id = null) {
        $id ? $request->validate(FetchQuest::$updateRules) : $request->validate(FetchQuest::$createRules);
        $data = $request->only([
            'name', 'is_active', 'image', 'remove_image', 'description',
            'greeting_message', 'request_message', 'completion_message',
            'expired_message', 'request_table_id', 'reward_table_id',
        ]);
        if ($id && $service->updateFetchQuest(FetchQuest::find($id), $data, Auth::user())) {
            flash('Fetch quest updated successfully.')->success();
        } elseif (!$id && $quest = $service->createFetchQuest($data, Auth::user())) {
            flash('Fetch quest created successfully.')->success();

            return redirect()->to('admin/data/fetch-quests/edit/'.$quest->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the quest deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFetchQuest($id) {
        $quest = FetchQuest::find($id);

        return view('admin.fetch_quests._delete_fetch_quest', [
            'quest' => $quest,
        ]);
    }

    /**
     * Deletes a fetch quest.
     *
     * @param App\Services\FetchQuestService $service
     * @param int                            $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFetchQuest(Request $request, FetchQuestService $service, $id) {
        if ($id && $service->deleteFetchQuest(FetchQuest::find($id), Auth::user())) {
            flash('Fetch quest deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/fetch-quests');
    }
}
