<?php

namespace denis909\yii;

class AttributeTypecastBehavior extends AttributeTypecastBehavior
{

    public $typecastAfterValidate = false;

    public $typecastBeforeValidate = false;

    public $typecastAfterSetAttributes = false;

    public $typecastSetAttributes = true;

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        $events = parent::events();

        if ($this->typecastBeforeValidate)
        {
            $events[ActiveRecord::EVENT_BEFORE_VALIDATE] = 'beforeValidate';
        }

        if ($this->typecastSetAttributes)
        {
            $events[ActiveRecord::EVENT_SET_ATTRIBUTES] = 'setAttributes';
        }

        if ($this->typecastAfterSetAttributes)
        {
            $events[ActiveRecord::EVENT_AFTER_SET_ATTRIBUTES] = 'afterSetAttributes';
        }

        return $events;
    }

    /**
     * Handles owner 'beforeValidate' event, ensuring attribute typecasting.
     * @param \yii\base\Event $event event instance.
     */
    public function beforeValidate($event)
    {
        $this->typecastAttributes();
    }

    /**
     * Handles owner 'afterSetAttributes' event, ensuring attribute typecasting.
     * @param \yii\base\Event $event event instance.
     */
    public function afterSetAttributes($event)
    {
        $this->typecastAttributes();
    }

    /**
     * Handles owner 'setAttributes' event, ensuring attribute typecasting.
     * @param \denis909\yii\SetAttributesEvent $event event instance.
     */
    public function setAttributes($event)
    {
        foreach($event->values as $key => $value)
        {
            if (!$event->safeOnly || $this->owner->isAttributeSafe($key))
            {
                $currentValue = $this->owner->{$key};

                $this->owner->{$key} = $value;

                $this->typecastAttributes([$key]);

                $event->values[$key] = $this->owner->{$key};

                $this->owner->{$key} = $currentValue;
            }
        }
    }

}