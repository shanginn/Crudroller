<?php

namespace Shanginn\Crudroller\Routing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Crudroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Http\Response
     */
    public function index(Model $model)
    {
        return response()->json($model::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Http\Response
     */
    public function store(FormRequest $request, Model $model)
    {
        $data = $request->toArray();

        $this->createWithRelationships($data, $model);

        return response()->json($model->toArray());
    }

    public function createWithRelationships(array $data, Model &$model)
    {
        $model->fill($data);

        $this->createBelongsToRelationships($data, $model);

        $model->save();

        $this->createHasManyRelationships($data, $model);
    }

    protected function createHasManyRelationships($data, Model &$model)
    {
        foreach ($model->getAvailableRelations() as $relation => $relationClass) {
            $relationSnake = Str::snake($relation);

            if ($relationClass === HasMany::class && isset($data[$relationSnake])) {
                $relatedData = $data[$relationSnake];
                /** @var HasMany $relationship */
                $relationship = $model->$relation();

                $relationship->create($relatedData);
            }
        }
    }

    protected function createBelongsToRelationships($data, Model &$model)
    {
        // If this model has BelongsTo relations
        // We need to create parent instance first
        foreach ($model->getAvailableRelations() as $relation => $relationClass) {
            $relationSnake = Str::snake($relation);
            if ($relationClass === BelongsTo::class && isset($data[$relationSnake])) {
                $relatedData = $data[$relationSnake];

                /** @var BelongsTo $relationship */
                $relationship = $model->$relation();

                $related = $relationship->getRelated();

                // Assume that this is a new relationship
                // and create it
                if (is_array($relatedData)) {
                    $related->fill($relatedData)->save();
                }
                // Or assume that we have existing relationship
                else {
                    $related->load($relatedData);
                }

                $relationship->associate($related);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Model $item)
    {
        return response()->json(
            $item->load('translations')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    public function update(FormRequest $request, Model $item)
    {
        if ($item->update($request->toArray())) {
            return response()->json($item);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $item)
    {
        if ($item->delete()) {
            return response(null, 204);
        }
    }
}
