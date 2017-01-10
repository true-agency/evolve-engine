<?php

namespace EvolveEngine\Post;

class PostTypeFactory
{
    /**
     * All resolvable custom post types
     *
     * @var array
     */
    protected $types = [];

    /**
     * Instance of each custom post type
     *
     * @var array
     */
    protected $resolvedTypes = [];

    public function __construct($types = [])
    {
        $this->types = $types;
    }

    /**
     * Get resolved custom post type
     *
     * @param  string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        if (isset($this->resolvedTypes[$id])) {
            return $this->resolvedTypes[$id];
        }

        return null;
    }

    /**
     * Register all available custom post types
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->types as $type) {
            if (!class_exists($type)) {
                trigger_error('Missing post type class: ' . $type . '.');
                continue;
            }
            $typeInstance = with(new $type)->init();
            $this->resolvedTypes[$typeInstance->id] = $typeInstance;
        }
    }

    /**
     * Alter where template is included if currently dealing with CPT
     *
     * @param  string $path
     *
     * @return string
     */
    public function includeTemplate($template_path)
    {
        $postType = get_post_type();
        if (in_array(get_post_type(), array_keys($this->resolvedTypes))) {
            $post = $this->get($postType);

            if ( $path = $post->getTemplatePath() ) {
                $template_path = $path;
            }
        }
        
        return $template_path;
    }

}