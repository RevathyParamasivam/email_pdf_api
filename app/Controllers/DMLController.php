<?php
namespace App\Controllers;

trait DMLController
{

    public function get($id = false)
    {
        $id = $id ? $id : 0;
        $data;
        if ($id === 0) {
            $data = $this->repository->findAll();
        } else {
            $data = $this->repository->findById($id);
        }
        return $this->message(200, $data);

    }

    public function save()
    {
        $req = $this->getDataFromUrl('json');
        if (!empty($req)) {
            $data = $this->repository->save($req);
            return $this->message(200, $data);
        } else {
            return $this->message(400, null, 'Date Not Available');
        }
    }

    public function update($cond, $data)
    {
        if (!empty($cond)) {
            $data = $this->repository->update($cond, $data);
            return $this->message(200, $data);
        } else {
            return $this->message(400, null, 'Date Not Available');
        }
    }

    public function delete($id)
    {
        $id = (int) $id;
        if ($id) {
            if ($this->repository->deleteOfId($id)) {
                return $this->message(200, $id, 'Success');
            } else {
                return $this->message(400, null, 'Failed to Delete');
            }

        }
    }

}
