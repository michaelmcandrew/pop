# Pop

A library for populating a CiviCRM site with fake data.

## Usage

Typical usage is via a command line tool like '[cv](https://github.com/civicrm/cv)'.

To populate a site with instructions in the file `/path/to/pop.yml`, type

```
cv pop /path/to/pop.yml
```

Pop is also available via composer:

`composer require michaelmcandrew/pop`

## Syntax

Pop files are a series of instructions written in yaml. Each instruction creates
one or more entities. Some examples will help explain the syntax.

You can use Pop to create 500 Individuals:

```yaml
- Individual: 500
```

Many instructions can be combined into a single file and will be carried out one after the other:

```yaml
- Organization: 500
- Individual: 5000
- Event: 80
- MembershipType: 5
- Membership: 500
```

Pop uses realistic default fields when creating entities and creates realistic  'child entities' when appropriate.

The defaults for each entity are defined in the [EntityDefault](src/Pop/EntityDefault) directory. The defaults for an Individual is as follows:

```yaml
fields:
  first_name: f.firstName # a realistic first name
  last_name: f.lastName # a realistic first name
children:
  - Address: 0-2  # creates up to 2 postal addresses
  - Email: 0-3 # creates up to 3 email addresses
  - Phone: 0-2 # creates up to 2 phone numbers
```

### Fields

Instead of using the defaults, you can specify the fields you want to create as follows:

```yaml
- Individual: 500
  fields:
    job_title: Fundraiser
```

### Children

You can also define child entities. The following example creates 500 donors and creates between 10 and 100 donations.

```yaml
- Individual: 500
  children:
  - Contribution: 10-100
```

Note that the count of entities can be specified as a range with lower and upper limits as in the Contribution example above.

### Choosing fields

Sometimes it is useful to supply a list of values for a field and have Pop choose one for you each time an entity is created:

```yaml
- Individual: 500
  fields:
    job_title:
      CEO: 1 # ~ 25 CEOs
      Membership manager: 3 # ~ 75 membership managers
      Event co-ordinator: 6 # ~ 150 event co-ordinators
      Fundraiser: 10 # ~ 250 fundraisers
```

Higher value fields are more likely to be picked.

### Using Faker

You can also ask Pop to use the [Faker library](https://github.com/fzaninotto/Faker) to generate field values. For example, you can use the Faker jobTitle method as follows:

```yaml
- Individual: 500
  fields:
    job_title: f.jobTitle
```

The syntax for invoking a faker method is 'f.' followed by the method name,
followed by any parameters, seperated by commas. Note that a capital F will
cause the first letter of the field to be capitalised. Below are some more examples of faker methods:

```yaml
first_name: f.firstName # a random first name
total_amount: f.randomFloat,2,1000,2000 # an amount between 1000 and 2000
receive_date: f.dateTimeBetween,-5 years,now # a date in the last 5 years
event_title: F.words,3,1 # three words
description: f.paragraph,1 # a paragraph
```

See https://github.com/fzaninotto/Faker for a full list of methods.

### Random entities

Sometimes it is useful to pick a random entity as the field value of another entity. This happens automatically for required fields (for example a Event and a Contact are randomly selected each time a Participant is created). Sometimes it is useful to request a random Entity tag, which you can do as follows:

```yaml
- EntityTag: 1
  fields:
    entity_table: civicrm_activity
    tag_id: r.Activity
```

### Random options

Required options are populated automatically. Sometimes, you may want to request that pop chooses a random option from those that are available. To do this, specify choose (only valid for fields that can be passed to [Entity].getoptions API).

```yaml
- Event: 1
  fields:
    participant_listing_id: choose
```

# Supported entities

The most common CiviCRM entities are tested to ensure they work with Pop. Other entities may or may not work. The entityProvider function in the [Pop test suite](https://github.com/michaelmcandrew/cv/blob/pop/tests/Command/PopCommandTest.php)) tests has the most up to date list of tested entities.

# Hopes and dreams

* Support more entities.
* Use twig as templating language for fields (in the same way that ansible uses Jinja)
* Add command to create a single set of entities (easier than reading a pop file), e.g. `civipop -e Individual -c 300`
* read from stdin
* allow fields to reference each other. So that, for example, email can be set as {$first_name}.{$last_name}@{current_employer}.org
* Asign all created entities to a batch each time pop is run so that they can be easily found and deleted in future.
