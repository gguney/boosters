<?php
namespace GGuney\Boosters;

use Validator;
use Redirect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GGuney\Brush\Brush;
use Illuminate\Support\Facades\Storage;

trait ControllerBooster
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

    /**
     * Set DataModel object.
     *
     * @param DataModel $DM
     */
    public function setDM($DM)
    {
        $this->DM = $DM;
    }

    /**
     * Constructor. Set Prefix for admin dashboard.
     */
    public function __construct()
    {
        $this->prefix = config('boosters.prefix');
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
                                $ex = $request->file($fileInputName)->extension();
                                if($ex == 'png' || $ex == 'jpg' || $ex == 'jpeg' || $ex == 'bmp'){
                                    Brush::make($realPath)->resize()->mark()->changeQuality()->clear();
                                    $subPath = config('boosters.photos_sub_path');
                                }
                                else
                                    $subPath = config('boosters.documents_sub_path');
                                $disk = config('boosters.disk');
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
            //return $validator;
            return Redirect::to($this->prefix . '/' . lcfirst($DM->getName() . '/create'))->withErrors($validator)->withInput($request->except('password'));

        } else {
            try{
                $this->save($request, $DM);
            }
            catch(Exception $e){
               echo false;
               return;
            }

            /*
            return Redirect::to($this->prefix . '/' . lcfirst($DM->getName()))
                           ->with('success', $DM->getName() . ' has been created.');*/
            echo true;
            return;
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
            return $validator;
    /*
            return Redirect::to($this->prefix . '/' . lcfirst($DM->getName() . '/' . $item->id . '/edit'))
                           ->withErrors($validator)
                           ->withInput($request->except('password'));
    */
        } else {
            try{
                $this->save($request, $DM, $item);
            }
            catch(Exception $e){
                echo false;
                return;
            }

            /*
            return Redirect::to($this->prefix . '/' . lcfirst($DM->getName()))
                           ->with('success', $DM->getName() . ' has been created.');*/
            echo true;
            return;

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

        $saved = $item->save();
        if(!$saved){
            echo false;
            return;
        }else{
            echo true;
            return;
        }
        // return Redirect::to($this->prefix.'/'.lcfirst($DM->getName().''))->with('success',$DM->getName().' has been created.');
    }
}

?>