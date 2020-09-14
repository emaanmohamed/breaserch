<?php


namespace App\Services;


use App\Models\Attachment;
use App\Models\User;
use App\Models\ResearchAttachmentRel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    public function getAttachments($request)
    {
        $userId = null;
        if (! is_null($request->addedBy)) {
            $user   = User::where('username', 'like', '%'.$request->addedBy.'%')
                ->orWhere('email', 'like', '%'.$request->addedBy.'%')->get();
            $userId = isset($user[0]->id) ? $user[0]->id : 'not-found';
        }

        $attachments = Attachment::with('user')
            ->filename($request->fileName)
            ->addedby($userId)
            ->date([$request->dateFromAttachment, $request->dateToAttachment])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return $attachments;

    }

    public function saveAttachment($request)
    {
        if ($request->hasFile('attachments')) {
            $attachment       = $request->attachments;
            $extension        = $attachment->getClientOriginalExtension();
            $originalFilename = $attachment->getClientOriginalName();
            $size             = $attachment->getClientSize();
            $serverFileName   = Str::random(6) . '_' . time();
            $path             = $attachment->storeAs('/', $serverFileName);

            $attach           = Attachment::create([
                'original_file_name' => $originalFilename,
                'server_file_name'   => $serverFileName,
                'size'               => $size,
                'file_extension'     => $extension
            ]);
            return $attach;
        }
        return false;
    }

    public function findAttachmentByDocId($documentId)
    {
        $attachment = ResearchAttachmentRel::where('research_doc_id', $documentId)->with('attachment')->get();

        return $attachment;
    }

    public function saveAttachmentAndGetFilename($request)
    {
        $data = [];
        if ($request->hasFile('attachments')) {
            $attachments = $request->file('attachments');
            foreach ($attachments as $attachment) {

                $extension        = $attachment->getClientOriginalExtension();
                $originalFilename = $attachment->getClientOriginalName();
                $size             = $attachment->getClientSize();
                $serverFileName   = Str::random(6) . '_' . time();
                $attachment->storeAs('/', $serverFileName);

                $attachment = Attachment::create([
                    'original_file_name' => $originalFilename,
                    'server_file_name'   => $serverFileName,
                    'size'               => $size,
                    'file_extension'     => $extension,
                ]);


                ResearchAttachmentRel::create([
                    'research_doc_id' => $request->docId,
                    'attachment_id'   => $attachment->id
                ]);

                $data[] = [
                    'original_file_name' => $originalFilename,
                    'server_file_name'   => $serverFileName,
                    'size'               => $size,
                    'file_extension'     => $extension
                ];
            }
            return $data;
        }

        return false;
    }



    public function updateAttachment($request)
    {
        if ($request->hasFile('attachments')) {
            $attachment        = $request->attachments;
            $extension         = $attachment->getClientOriginalExtension();
            $originalFilename  = $attachment->getClientOriginalName();
            $size              = $attachment->getClientSize();
            $serverFileName    = Str::random(6) . '_' . time();
            $path              = $attachment->storeAs('/', $serverFileName);
            $updatedAttachment = Attachment::findOrFail($request->id)->update([
                'original_file_name' => $originalFilename,
                'server_file_name'   => $serverFileName,
                'size'               => $size,
                'file_extension'     => $extension
            ]);
            return $updatedAttachment;
        }
        return false;
    }


    public function download($attachmentId)
    {
        $attachment = Attachment::findOrFail($attachmentId);
        return Storage::download($attachment->server_file_name, $attachment->original_file_name);
    }

    public function removeAttachmentDocRel($attachId)
    {
        $attachment = ResearchAttachmentRel::where('id', $attachId)->delete();
        return $attachment;
    }

}
