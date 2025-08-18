<?php
/**
 * A data component that holds information for rendering.
 * It extends the base Component class to be part of the system.
 */
class RenderDataComponent extends Component
{
    /** @var string The color to draw the object. */
    public string $color;
    /** @var int The size (width and height) of the object. */
    public int $size;

    /**
     * Constructs the RenderDataComponent.
     * @param string $color The hex or named color.
     * @param int $size The size in pixels.
     */
    public function __construct(string $color, int $size)
    {
        $this->color = $color;
        $this->size = $size;
    }
}