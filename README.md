# Twig Let

The package provides a let tag for Twig.

```php
{% let foo = bar %}
    {% else %}
{% endlet %}
```

## Questions?

### What is a let tag?

A let tag is like a set tag, but evaluates the expression before assignment.

### What else?

The let tag is scoped, which means the assigned variable exists only within the body of the tag. To make it outlive the scope, you need
to assign its value to an outer variable.

### And?

The else branch is optional.



