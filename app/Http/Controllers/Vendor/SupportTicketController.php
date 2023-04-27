<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\SupportTicket;
use App\Models\SupportTicketStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;

class SupportTicketController extends Controller
{
    //index
    public function index(Request $request)
    {
        $s_status = SupportTicketStatus::first();
        if ($s_status->support_ticket_status != 'active') {
            return redirect()->route('vendor.dashboard');
        }

        $status = null;
        if ($request->filled('status')) {
            $status = $request['status'];
        }

        $collection = SupportTicket::where('vendor_id', Auth::guard('vendor')->user()->id)->when($status, function ($query, $status) {
            return $query->where('status',  $status);
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('vendors.support_ticket.index', compact('collection'));
    }
    //create
    public function create()
    {
        $s_status = SupportTicketStatus::first();
        if ($s_status->support_ticket_status != 'active') {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendors.support_ticket.create');
    }
    //store
    public function store(Request $request)
    {
        $rules = [
            'email' => 'required',
            'subject' => 'required',
        ];

        if ($request->hasFile('attachment')) {
            $rules['attachment'] = 'mimes:zip';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $in = $request->all();
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $filename = uniqid() . '.' . $attachment->getClientOriginalExtension();
            $attachment->move(public_path('assets/admin/img/support-ticket/attachment/'), $filename);
            $in['attachment'] = $filename;
        }
        $in['vendor_id'] = Auth::guard('vendor')->user()->id;
        $in['ticket_number'] = uniqid();
        SupportTicket::create($in);

        Session::flash('success', 'Support Ticket Created Successfully..!');
        return back();
    }
    //message
    public function message($id)
    {
        $s_status = SupportTicketStatus::first();
        if ($s_status->support_ticket_status != 'active') {
            return redirect()->route('vendor.dashboard');
        }
        $ticket = SupportTicket::findOrFail($id);
        if ($ticket->vendor_id != Auth::guard('vendor')->user()->id) {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendors.support_ticket.messages', compact('ticket'));
    }
    public function zip_file_upload(Request $request)
    {
        $file = $request->file('file');
        $allowedExts = array('zip');
        $rules = [
            'file' => [
                function ($attribute, $value, $fail) use ($file, $allowedExts) {
                    $ext = $file->getClientOriginalExtension();
                    if (!in_array($ext, $allowedExts)) {
                        return $fail("Only zip file supported");
                    }
                },
                'max:20000'
            ],
        ];

        $messages = [
            'file.max' => ' zip file may not be greater than 5 MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/front/temp/'), $filename);
            $input['file'] = $filename;
        }

        return response()->json(['data' => 1]);
    }
    public function ticketreply(Request $request, $id)
    {
        $s_status = SupportTicketStatus::first();
        if ($s_status->support_ticket_status != 'active') {
            return redirect()->route('vendor.dashboard');
        }
        $file = $request->file('file');
        $allowedExts = array('zip');
        $rules = [
            'reply' => 'required',
            'file' => [
                function ($attribute, $value, $fail) use ($file, $allowedExts) {

                    $ext = $file->getClientOriginalExtension();
                    if (!in_array($ext, $allowedExts)) {
                        return $fail("Only zip file supported");
                    }
                },
                'max:20000'
            ],
        ];

        $messages = [
            'file.max' => ' zip file may not be greater than 5 MB',
        ];

        $request->validate($rules, $messages);
        $input = $request->all();

        $reply = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->reply);
        $input['reply'] = Purifier::clean($reply);
        $input['vendor_id'] = Auth::guard('vendor')->user()->id;

        $input['support_ticket_id'] = $id;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/admin/img/support-ticket/'), $filename);
            $input['file'] = $filename;
        }

        $data = new Conversation();
        $data->create($input);

        $files = glob('assets/front/temp/*');
        foreach ($files as $file) {
            unlink($file);
        }

        SupportTicket::where('id', $id)->update([
            'last_message' => Carbon::now(),
            'status' => 2,
            'vendor_id' => Auth::guard('vendor')->user()->id
        ]);

        Session::flash('success', 'Message Sent Successfully');
        return back();
    }

    //delete
    public function delete($id)
    {
        //delete all support ticket
        $support_ticket = SupportTicket::find($id);
        if ($support_ticket) {
            //delete conversation 
            $messages = $support_ticket->messages()->get();
            foreach ($messages as $message) {
                @unlink(public_path('assets/admin/img/support-ticket/' . $message->file));
                $message->delete();
            }
            @unlink(public_path('assets/admin/img/support-ticket/attachment/') . $support_ticket->attachment);
            $support_ticket->delete();
        }
        Session::flash('success', 'Support Ticket Deleted Successfully..!');
        return back();
    }

    public function bulk_delete(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $support_ticket = SupportTicket::find($id);
            if ($support_ticket) {
                //delete conversation 
                $messages = $support_ticket->messages()->get();
                foreach ($messages as $message) {
                    @unlink(public_path('assets/admin/img/support-ticket/' . $message->file));
                    $message->delete();
                }
                @unlink(public_path('assets/admin/img/support-ticket/attachment/') . $support_ticket->attachment);
                $support_ticket->delete();
            }
        }
        Session::flash('success', 'Support Tickets are Deleted Successfully..!');
        return Response::json(['status' => 'success'], 200);
    }
}
