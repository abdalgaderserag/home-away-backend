<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
    public function uploadFile(UploadFileRequest $request)
    {
        $attachment = new Attachment();
        if ($request->hasFile('attachment')) {
            $file = $request->attachment;
            /*if ($request->user_id) {
                $path = Storage::put("user/{$request->uploader}/profile/{$request->user_id}", $file);
                $attachment->user_id = $request->user_id;
                $attachment->url = $path;
            }
            if ($request->project_id) {
                $path = Storage::put("user/{$request->uploader}/project/{$request->project_id}", $file);
                $attachment->project_id = $request->project_id;
                $attachment->url = $path;
            }
            if ($request->message_id) {
                $path = Storage::put("user/{$request->uploader}/messages/{$request->message_id}", $file);
                $attachment->message_id = $request->message_id;
                $attachment->url = $path;
            }
            if ($request->milestone_id) {
                $path = Storage::put("user/{$request->uploader}/milestone/{$request->milestone_id}", $file);
                $attachment->milestone_id = $request->milestone_id;
                $attachment->url = $path;
            }
            if ($request->verification_id) {
                $path = Storage::put("user/{$request->uploader}/verification/{$request->verification_id}", $file);
                $attachment->verification_id = $request->verification_id;
                $attachment->url = $path;
            }*/
            $path = Storage::putFile("/data", $file);
            $attachment->url = $path;
            $attachment->owner_id = Auth::id();
            $attachment->save();
        } else {
            return response()->json(['message' => 'File not found'], Response::HTTP_BAD_REQUEST);
        }
        return response(["message" => $attachment], Response::HTTP_OK);
    }

    public function getFile($id)
    {
        $attachment = Attachment::find($id);
        if (!$attachment) {
            return response()->json(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }
        if (!$this->haveAccess($attachment))
            return response()->json(['message' => "You don't have access to this file."], Response::HTTP_FORBIDDEN);

        $file = Storage::get($attachment->url);
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . basename($attachment->url) . '"',
        ];
        return response($file, Response::HTTP_OK, $headers);
    }

    public function removeUploadedFile($id)
    {
        $attachment = Attachment::find($id);
        if (!$attachment) {
            return response()->json(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }
        defer(function () use ($attachment) {
            Storage::delete($attachment->url);
            $attachment->delete();
        });
        return response()->noContent();
    }

    private function haveAccess(Attachment $attachment)
    {
        $user_id  = Auth::id();
        if ($user_id === $attachment->owner_id) {
            return true;
        }

        if (!empty($attachment->user_id) || !empty($attachment->project_id)) {
            return true;
        }

        if ($attachment->message->sender_id === $user_id || $attachment->message->receiver_id === $user_id)
            return true;

        return ($attachment->milestone->offer->user_id === $user_id);
    }
}
