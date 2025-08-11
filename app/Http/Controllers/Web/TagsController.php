<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateTagData;
use App\Data\UpdateTagData;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Repositories\GivingTypeRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TagsController extends Controller
{
    public function __construct(
        protected GivingTypeRepository $tagRepository,
    ) {
    }

    public function index(Request $request): Response
    {
        $onlyTrashed = (bool) $request->boolean('trashed');
        return Inertia::render('Tags/Index', [
            'tags' => $this->tagRepository->paginate($onlyTrashed),
            'showingTrashed' => $onlyTrashed,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tags/Create');
    }

    public function store(CreateTagData $data): RedirectResponse
    {
        $this->tagRepository->create($data->toArray());
        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    public function edit(Tag $tag): Response
    {
        return Inertia::render('Tags/Edit', [
            'tag' => $tag,
        ]);
    }

    public function update(UpdateTagData $data, Tag $tag): RedirectResponse
    {
        $this->tagRepository->update($tag, $data->toArray());
        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $this->tagRepository->delete($tag);
        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }

    public function restore(int $tagId): RedirectResponse
    {
        $tag = Tag::onlyTrashed()->findOrFail($tagId);
        $this->tagRepository->restore($tag);
        return redirect()->route('tags.index', ['trashed' => 1])->with('success', 'Tag restored successfully.');
    }
}
