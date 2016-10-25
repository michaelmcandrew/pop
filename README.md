# Pop

A library for populating a CiviCRM site with fake data

# Usage

Typical usage is via a command line tool like '[cv](https://github.com/civicrm/cv)'.

To populate a site with instructions in the file `/path/to/pop.yml`, type

```
cv pop /path/to/pop.yml
```

# Syntax

Pop files are a series of instructions written in yaml. Each instruction creates
one or more entities.

## Simple examples

Create 5000 Individuals

```yaml
- Individual: 5000 # Sensible default fields will be created
```

Create a few different entities

```yaml
- Organization: 50
- MembershipType: 5
- Membership: 500
```

Specify **fields**
```yaml
- Individual: 5000
  fields:
    job_title: f.jobTitle # see *Accessing Faker Methods* below
```


Specify **children**
```yaml
- Event: 20
  children: # When adding children causes the 'parent' id is passed to the child entity
  - Participant: 100

```

## Supported entities

A subset of CiviCRM entities are tested to work with Pop. These are listed ***HERE***.

Non tested entities may also work but are not garunteed to do so.

Contributions that get more entities working are welcome.

## Defaults

Many entities come with sensible default fields and default children. For example, Individuals are created with a fake first and last name, 0-2 addresses, 0-3 emails and 0-2 phones.

```yaml
# src/EntityDefault/Individual.yml
fields:
  first_name: f.firstName
  last_name: f.lastName
children:
  - Address: 0-2
  - Email: 0-3
  - Phone: 0-2
```

## Choosing from a list

When a field is a yaml dictionary, Pop will choose a key from the dictionary. Keys with higher values are more likely to be selected.

```yaml
- Individual: 500
  fields:
    job_title:
      CEO: 1 # ~ 25 CEOs
      Membership manager: 3 # ~ 75 membership managers
      Event co-ordinator: 6 # ~ 150 event co-ordinators
      Fundraiser: 10 # ~ 250 fundraisers
```

## Modifiers

Pop will search for fields matching certain patterns and replace them as follows

### Faker methods

`f.<methodName>[,param]...`

will call a [faker method](https://github.com/fzaninotto/Faker) with optional parameters

```yaml
- Individual: 50
  fields:
    middle_name: f.firstName # will create middle names
- Contribution: 500
  fields:
    total_amount: f.randomFloat,2,1000,2000 # contributions between 1k and 2k
    receive_date: f.dateTimeBetween,-5 years,now # in the last 5 years
```

### Random entities

`r.<Enity>`

Returns a random entity id.

### Random options

`choose`

Returns a random option key for this field (only valid for fields that can be passed to [Entity].getoptions API).

# Hopes and dreams

* More support for entities. For many, the issues can be solved by updating the underlying API (adding api.required for certain fields, etc.)
* use twig as templating language for fields (in the same way that ansible uses Jinja)
* allow fields to duplicate each other. So that, for example, email can be set as {$first_name}.{$last_name}@example.org
* create a batch for all entities each time pop is run so that they can be easily found and deleted
