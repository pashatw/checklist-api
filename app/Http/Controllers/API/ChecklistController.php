<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Illuminate\Http\Response;
use App\Models\ChecklistModel;
use App\Models\AttributesModel;
use App\Models\ItemAttributesModel;
use App\Http\Resources\Checklist;
use App\Http\Resources\ChecklistCollection;
use Carbon\Carbon;

class ChecklistController extends Controller
{
    public function get(Request $request, $checklistId)
    {
    	try {

    		$validate = Validator::make($request->all(), [
				'include' => 'nullable|regex:/^items$/i',
		    ]);

		    if ($validate->fails()) {
		    	return parent::failResponse($validate->errors(), Response::HTTP_BAD_REQUEST);
		    }

    		$include = !empty($request->get('include')) ? $request->get('include') : null;
    		$filter = !empty($request->get('filter')) ? $request->get('filter') : null;
    		$sort = !empty($request->get('sort')) ? $request->get('sort') : null;
    		$field = !empty($request->get('field')) ? $request->get('field') : null;
    		$page_limit = !empty($request->get('page_limit')) ? $request->get('page_limit') : 10;
    		$page_offset = !empty($request->get('page_offset')) ? $request->get('page_offset') : 0;

	    	$checklist = ChecklistModel::with('attributes', 'attributes.items')->find($checklistId); 
	    	$checklistResource = !empty($checklist) ? new Checklist($checklist) : null;

            return parent::successResponse($checklistResource);
	    } catch (\Exception $e) {
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
	    } catch (\Throwable $e) {
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $checklistId)
    {
    	DB::beginTransaction();
    	try {
    		$validate = Validator::make($request->json()->all(), [
				'data.attributes.object_domain' => 'required',
				'data.attributes.object_id'     => 'required',
				'data.attributes.description'   => 'required',
		    ]);

		    if ($validate->fails()) {
		    	return parent::failResponse($validate->errors(), Response::HTTP_BAD_REQUEST);
		    }

		    $checklist = ChecklistModel::find($checklistId);

		    if (empty($checklist)) {
                DB::rollback();
                return parent::failResponse("Failed to save! Checklist not found.", Response::HTTP_BAD_REQUEST);
	    	}

	    	$checklist->update([
	    		'type' => $request->json('data')['type'],
	    	]);

	    	$checklist->attributes()->update([
	    		'description' => ($request->json('data')['attributes']['description']) ? $request->json('data')['attributes']['description'] : "",
	    		'is_completed' => ($request->json('data')['attributes']['is_completed']) ? true : false,
	    		'completed_at' => ($request->json('data')['attributes']['is_completed']) ? Carbon::now()->format('Y-m-d H:i:s') : null,
	    	]);

	    	$checklist = ChecklistModel::with('attributes')->find($checklistId); 
	    	DB::commit();
            return parent::successResponse(new Checklist($checklist));

	    } catch (\Exception $e) {
	    	DB::rollback();
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
	    } catch (\Throwable $e) {
	    	DB::rollback();
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(Request $request, $checklistId)
    {
    	DB::beginTransaction();
    	try {

		    $checklist = ChecklistModel::find($checklistId);

		    if (empty($checklist)) {
                DB::rollback();
                return parent::failResponse("Failed to delete! Checklist not found.", Response::HTTP_BAD_REQUEST);
	    	}

	    	$checklist = ChecklistModel::find($checklistId)->delete(); 
	    	DB::commit();
            return parent::defaultResponse([], Response::HTTP_NO_CONTENT);
	    } catch (\Exception $e) {
	    	DB::rollback();
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
	    } catch (\Throwable $e) {
	    	DB::rollback();
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request)
    {
    	DB::beginTransaction();
    	try {
    		$validate = Validator::make($request->json()->all(), [
				'data.attributes.object_domain' => 'required',
				'data.attributes.object_id'     => 'required',
				'data.attributes.description'   => 'required',
		    ]);

		    if ($validate->fails()) {
		    	return parent::failResponse($validate->errors(), Response::HTTP_BAD_REQUEST);
		    }

		 	$saveChecklist = ChecklistModel::create(['type' => 'checklists']);
		 	if (!$saveChecklist) {
                DB::rollback();
                return parent::failResponse("Failed to save checklist", Response::HTTP_BAD_REQUEST);
            }

	    	$saveAttr = $this->saveAttributes($saveChecklist, $request->json('data')['attributes']);
    		if (!$saveAttr) {
                DB::rollback();
                return parent::failResponse("Failed to save attributes", Response::HTTP_BAD_REQUEST);  
    		}

            $saveItems = $this->saveItems($saveAttr, $request->json('data')['attributes']);
            if (!$saveItems) {
                DB::rollback();
                return parent::failResponse("Failed to save items", Response::HTTP_BAD_REQUEST);
            }

	    	$checklist = ChecklistModel::with('attributes')->find($saveChecklist->id); 
	    	DB::commit();
            return parent::successResponse(new Checklist($checklist));

	    } catch (\Exception $e) {
	    	DB::rollback();
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
	    } catch (\Throwable $e) {
	    	DB::rollback();
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAll(Request $request)
    {
    	try {
    		$include = !empty($request->get('include')) ? $request->get('include') : null;
    		$filter = !empty($request->get('filter')) ? $request->get('filter') : null;
    		$sort = !empty($request->get('sort')) ? $request->get('sort') : null;
    		$field = !empty($request->get('field')) ? $request->get('field') : null;
    		$page_limit = !empty($request->get('page_limit')) ? $request->get('page_limit') : 10;
    		$page_offset = !empty($request->get('page_offset')) ? $request->get('page_offset') : 0;

	    	$checklist = ChecklistModel::with('attributes')->paginate(5);
	    	return new ChecklistCollection($checklist);
	    } catch (\Exception $e) {
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
	    } catch (\Throwable $e) {
	    	return parent::failResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    function saveAttributes($checklist, $dataAttr)
    {
        return AttributesModel::create([
            'checklist_id'  => $checklist->id,
            'due'           => date('Y-m-d H:i:s', strtotime($dataAttr['due'])),
            'object_domain' => $dataAttr['object_domain'],
            'object_id'     => $dataAttr['object_id'],
            'urgency'       => $dataAttr['urgency'],
            'description'   => $dataAttr['description'],
        ]);
    }

    function saveItems($attributes, $dataAttr)
    {
    	if (isset($dataAttr['items']) && !empty($dataAttr['items'])) {
            foreach ($dataAttr['items'] as $key => $item) {
                $dataItems[] = [
                    'attribute_id' => $attributes->id,
                    'is_completed' => !empty($attributes->is_completed) ? true : false,
                    'due' => $attributes->due,
                    'urgency' => !empty($attributes->urgency) ? $attributes->urgency : 0,
                    'asignee_id' => "",
                    'description' => $item,
                    'task_id' => $dataAttr['task_id'],
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ];
    		}

            return ItemAttributesModel::insert($dataItems);
    	}

    	return true;
    }
}
