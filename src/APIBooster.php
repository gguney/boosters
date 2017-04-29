<?php
namespace GGuney\Boosters;

use Validator;
use Redirect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GGuney\Brush\Brush;
use Illuminate\Support\Facades\Storage;

trait APIBooster
{

    /**
     * DataModel object.
     *
     * @var DataModel
     */
    protected $DM;

    /**
     * Admin dashboard prefix variable.
     *
     * @var string
     */
    private $prefix;

    private $response;

    /**
     * Set DataModel object.
     *
     * @param DataModel $DM
     */
    public function setDM($DM)
    {
        $this->DM = $DM;
        $this->response = [];
    }

    /**
     * Constructor. Set Prefix for admin dashboard.
     */
    public function __construct()
    {
        $this->prefix = config('boosters.api_prefix');
    }

    public function respond($paginator = null, $statusCode = 200, $message = null, $error = null)
    {
        $error = ['code' => $statusCode, 'message' => $message];
        $data = [$this->DM->getName() => $paginator[0]->items()];
        $this->response = [
            'status' => (isset($error) ? 'success' : 'fail'),
            'error' => ['code' => $statusCode, 'message' => $message],
            'data' => $data,
            'link' => $this->links($paginator[0])
        ];
        return response()
            ->json($this->response, $statusCode)
            ->header('Content-Type', 'application/vnd.api+json');
    }

    public function links($paginator)
    {
        $links = [
            'self' => $paginator->url($paginator->currentPage()),
            'first' => $paginator->url(1),
            'prev'  => $paginator->previousPageUrl(),
            'next'  =>  $paginator->nextPageUrl(),
            'last'  => $paginator->url($paginator->lastPage()),
            'per_page' =>  $paginator->perPage(),
            'current_page' =>  $paginator->currentPage(),
            'last_page' =>  $paginator->lastPage(),
        ];
        return $links;
    }

    public function addHeader($array)
    {

    }

    /**
     * Fill item with request and save.
     *
     * @param  Illuminate\Http\Request $request
     * @param  DataModel $DM
     * @param  Model $item
     *
     * @return void
     */
    private function save($request, $DM, $item = null)
    {
        $dataModel = $DM;
        if (!isset($item)) {
            $path = $dataModel->getModelsPath() . $dataModel->getModelName();
            $item = (new $path);
        }
        $columns = $dataModel->getColumns();

        $formFields = $dataModel->getFormFields();
        $nonEditableFields = $dataModel->getNonEditableFields();

        foreach ($formFields as $key => $value) {
            if (!in_array($value, $nonEditableFields)) {
                if ($value != 'password') {
                    if ($columns[$value]->get('type') == 'file') {
                        $originals = $item->getOriginal();
                        if (isset($originals) && !empty($originals)) {
                            $originalFile = $originals[$columns[$value]->get('name')];
                        }
                        $fileInputName = $columns[$value]->get('name');
                        $fileInputLinkName = $columns[$value]->get('name') . '_url';

                        if (isset($request->$fileInputName) && $request->$fileInputName != "") {
                            if ($request->file($fileInputName)
                                        ->isValid()
                            ) {
                                $path = $request->$fileInputName->path();
                                $realPath = $path;
                                Brush::make($realPath)
                                     ->resize()
                                     ->mark()
                                     ->changeQuality()
                                     ->clear();
                                $disk = config('boosters.disk');
                                $subPath = config('boosters.sub_path');
                                switch ($disk) {
                                    case 'local':
                                        $path = $request->$fileInputName->store($subPath);
                                        break;
                                    case 's3':
                                        $path = $request->$fileInputName->store($subPath, 's3');
                                        break;
                                    default:
                                        $path = $request->$fileInputName->store($subPath);
                                        break;
                                }
                            }
                        } else if (isset($request->$fileInputLinkName) && $request->$fileInputLinkName != "") {
                            $path = $request->input($columns[$value]->get('name') . '_url');
                        } else {
                            $path = trim("");
                        }
                        if (isset($path) && isset($originalFile) && $originalFile != $path) {
                            $item->$value = $path;
                        } else if (!isset($path) || !isset($originalFile)) {
                            $item->$value = $path;
                        }

                    } else {
                        if ($columns[$value]->get('type') != 'checkbox') {
                            $item->$value = $request->input($columns[$value]->get('name'));
                        } else {
                            $item->$value = ($request->input($columns[$value]->get('name')) !== null) ? 1 : 0;
                        }
                    }
                } else {
                    $originals = $item->getOriginal();

                    if (empty($originals))//create
                    {
                        $item->$value = bcrypt($request->input($columns[$value]->get('name')));
                    } else if ($originals['password'] != $request->input($columns[$value]->get('name'))) {
                        $item->$value = bcrypt($request->input($columns[$value]->get('name')));
                    }
                }
            }
        }
        $item->save();
    }

    /**
     * Store item from request.
     *
     * @param  Illuminate\Http\Request $request
     * @param  DataModel $DM
     *
     * @return Redirect
     */
    public function storeAction($request, $DM)
    {
        $validator = Validator::make($request->all(), $DM->getRules());
        if ($validator->fails()) {
            return Redirect::to($this->prefix . '/' . lcfirst($DM->getName() . '/create'))
                           ->withErrors($validator)
                           ->withInput($request->except('password'));
        } else {
            $this->save($request, $DM);
            return Redirect::to($this->prefix . '/' . lcfirst($DM->getName()))
                           ->with('success', $DM->getName() . ' has been created.');
        }
    }

    /**
     * Update item from request.
     *
     * @param  Illuminate\Http\Request $request
     * @param  DataModel $DM
     * @param  Model $item
     *
     * @return Redirect
     */
    public function updateAction($request, $DM, $item)
    {
        $validator = Validator::make($request->all(), $DM->getRules());
        if ($validator->fails()) {
            return Redirect::to($this->prefix . '/' . lcfirst($DM->getName() . '/' . $item->id . '/edit'))
                           ->withErrors($validator)
                           ->withInput($request->except('password'));
        } else {
            $this->save($request, $DM, $item);
            return Redirect::to($this->prefix . '/' . lcfirst($DM->getName()))
                           ->with('success', $DM->getName() . ' has been created.');
        }
    }

    /**
     * Restore deleted item from request.
     *
     * @param  Illuminate\Http\Request $request
     * @param  boolean $withUser
     *
     * @return Redirect
     */
    public function restore($request, $withUser = false)
    {
        $urlParser = $this->urlParser;
        $lastModelNameWithPath = $urlParser->last()->modelNameWithPath;
        $item = (new $lastModelNameWithPath)->find($urlParser->last()->id);
        $dataModel = $urlParser->last()->dataModel;

        $item->deleted_at = null;
        if ($withUser) {
            $item->deleted_by = - 1;
        }
        $item->save();

        return Redirect::to($this->prefix . '/' . $dataModel->getName())
                       ->with('success', $dataModel->getName() . ' has been restored.');
    }

    /**
     * Soft delete item from id.
     *
     * @param  DataModel $DM
     * @param  int $id
     * @param  boolean $withUser
     *
     * @return Redirect
     */
    public function destroyAction($DM, $id, $withUser = false)
    {
        $dataModel = $DM;
        $path = $dataModel->getModelsPath() . $dataModel->getModelName();
        $item = (new $path)->find($id);
        $item->deleted_at = Carbon::now();
        if ($withUser) {
            $item->deleted_by = Auth::user()->id;
        }
        $item->save();

        return Redirect::to($this->prefix . '/' . lcfirst($DM->getName() . ''))
                       ->with('success', $DM->getName() . ' has been created.');
    }
}

?>