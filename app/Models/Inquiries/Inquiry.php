<?php

namespace App\Models\Inquiries;

use App\Extenders\Models\BaseModel as Model;

use App\Models\Users\Admin;

use App\Notifications\Admin\UserInquiry;

use Notification;

class Inquiry extends Model
{


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'purpose' => $this->purpose,
        ];
        
        return $searchable;
    }

    /**
     * @Render
     */
    public function renderShowUrl($prefix = 'admin') {
        $route = $this->id;

        if ($prefix == 'web') {
            $route = [$this->id, $this->slug];
        }

        return route($prefix . '.inquiries.show', $route);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.inquiries.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.inquiries.restore', $this->id);
    }


    public function renderWebHome() {
        return '/';
    }

    /**
     * @Setters
     */
    public static function store($request, $item = null, $columns = ['fullname', 'contact_number', 'email', 'purpose', 'message'])
    {
        $vars = $request->only($columns);
        
        if (!$item) {
            $item = static::create($vars);
        } else {
            $item->update($vars);
        }

        return $item;
    }


    /**
     * @Notifications
     */
    public function sendInquiryNotification($item) {
        // $ids = Permission::getUsersByPermission(['admin.inquiry.crud']);
        // $admins = Admin::whereIn('id', $ids)->get();

        // if (count($admins)) {
        //     Notification::send($admins, new UserInquiry($this, 'admin'));
        // }

        $admins = Admin::all();

        foreach($admins as $admin) {
            $admin->notify(new UserInquiry($item));
        }


    }

}
