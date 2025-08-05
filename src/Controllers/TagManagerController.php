<?php

namespace admin\tags\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use admin\tags\Requests\TagCreateRequest;
use admin\tags\Requests\TagUpdateRequest;
use admin\tags\Models\Tag;
use admin\admin_auth\Traits\HasSeo;

class TagManagerController extends Controller
{
    use HasSeo;

    public function __construct()
    {
        $this->middleware('admincan_permission:tags_manager_list')->only(['index']);
        $this->middleware('admincan_permission:tags_manager_create')->only(['create', 'store']);
        $this->middleware('admincan_permission:tags_manager_edit')->only(['edit', 'update']);
        $this->middleware('admincan_permission:tags_manager_view')->only(['show']);
        $this->middleware('admincan_permission:tags_manager_delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        try {
            $tags = Tag::filter($request->query('keyword'))
                ->filterByStatus($request->query('status'))
                ->sortable()
                ->latest()
                ->paginate(Tag::getPerPageLimit())
                ->withQueryString();

            return view('tag::admin.index', compact('tags'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load tags: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('tag::admin.createOrEdit');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load tags: ' . $e->getMessage());
        }
    }

    public function store(TagCreateRequest $request)
    {
        try {
            $requestData = $request->validated();

            $tag = Tag::create($requestData);
            $this->saveSeo(Tag::class, $tag->id, $requestData);
            return redirect()->route('admin.tags.index')->with('success', 'Tag created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load tags: ' . $e->getMessage());
        }
    }

    /**
     * show tag details
     */
    public function show(Tag $tag)
    {
        try {
            $seo = $this->getSeo($tag);
            return view('tag::admin.show', compact('tag','seo'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load tags: ' . $e->getMessage());
        }
    }

    public function edit(Tag $tag)
    {
        try {
            $seo = $this->getSeo($tag);
            return view('tag::admin.createOrEdit', compact('tag','seo'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load tag for editing: ' . $e->getMessage());
        }
    }

    public function update(TagUpdateRequest $request, Tag $tag)
    {
        try {
            $requestData = $request->validated();

            $tag->update($requestData);
            $this->saveSeo(Tag::class, $tag->id, $requestData);
            return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load tag for editing: ' . $e->getMessage());
        }
    }

    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();
            return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $tag = Tag::findOrFail($request->id);
            $tag->status = $request->status;
            $tag->save();

            // create status html dynamically        
            $dataStatus = $tag->status == '1' ? '0' : '1';
            $label = $tag->status == '1' ? 'Active' : 'InActive';
            $btnClass = $tag->status == '1' ? 'btn-success' : 'btn-warning';
            $tooltip = $tag->status == '1' ? 'Click to change status to inactive' : 'Click to change status to active';

            $strHtml = '<a href="javascript:void(0)"'
                . ' data-toggle="tooltip"'
                . ' data-placement="top"'
                . ' title="' . $tooltip . '"'
                . ' data-url="' . route('admin.tags.updateStatus') . '"'
                . ' data-method="POST"'
                . ' data-status="' . $dataStatus . '"'
                . ' data-id="' . $tag->id . '"'
                . ' class="btn ' . $btnClass . ' btn-sm update-status">' . $label . '</a>';

            return response()->json(['success' => true, 'message' => 'Status updated to ' . $label, 'strHtml' => $strHtml]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }
}
