<?php
/**
 * Created by PhpStorm.
 * User: gg
 * Date: 13/03/2017
 * Time: 21:28
 */

namespace GGuney\Boosters;

trait ComponentBooster
{

    protected $views;
    protected $slot;

    public function __construct()
    {

    }

    public function addBreak()
    {
        return $this->add("<br>");
    }

    public function addTable($DM, $items)
    {
        return $this->add(View(config('boosters.table_component_path'))->with(['DM' => $DM, 'items' => $items]));
    }

    public function addForm($DM, $item = null)
    {
        return $this->add(View(config('boosters.form_component_path'))->with(['DM' => $DM, 'item' => $item]));
    }

    public function addShow($DM, $item)
    {
        return $this->add(View(config('boosters.show_component_path'))->with(['DM' => $DM, 'item' => $item]));
    }

    public function add($view)
    {
        $this->views[] = $view;
        return $this;
    }

    public function get()
    {
        foreach ($this->views as $view) {
            $this->slot .= $view;
        }
        return $this->slot;
    }
}